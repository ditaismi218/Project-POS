<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\PenerimaanBarang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function index()
    {
        Log::info('Mengakses halaman daftar penjualan oleh user', ['user_id' => Auth::id()]);

        $penjualan = Penjualan::with('user', 'member')->orderBy('created_at', 'desc')->get();
        Log::info('Data penjualan diambil', ['jumlah_penjualan' => $penjualan->count()]);

        $members = Member::all();
        $produk = Produk::with([
            'penerimaanBarang' => function ($query) {
                $query->orderBy('tgl_masuk', 'desc');
            }
        ])->get()->map(function ($p) {
            $p->harga_jual = $p->penerimaanBarang->first()->harga_jual ?? 0;
            return $p;
        });

        return view('penjualan.index', compact('penjualan', 'members', 'produk'));
    }

    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'member_id' => 'nullable|exists:member,id',
            'status' => 'in:lunas,belum_lunas,batal,pending',
            'cart' => 'required|array',
            'cart.*.produk_id' => 'required|exists:produk,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        Log::info('Menerima permintaan penyimpanan penjualan', $validated);

        DB::beginTransaction();
        try {
            $totalBayar = 0;
            $tgl_faktur = now();
            $status = $validated['status'] ?? 'pending';

            // Buat transaksi penjualan baru
            $penjualan = Penjualan::create([
                'user_id' => Auth::id(),
                'member_id' => $validated['member_id'] ?? null,
                'tgl_faktur' => $tgl_faktur,
                'total_bayar' => 0, // Nanti di-update
                'status' => $status,
            ]);

            Log::info('Penjualan berhasil dibuat', ['penjualan_id' => $penjualan->id]);

            // Simpan ke ActivityLog
            ActivityLog::create([
                'action' => 'create',
                'description' => 'Penjualan baru dibuat',
                'data' => json_encode(['penjualan_id' => $penjualan->id])
            ]);

            // Proses setiap item dalam keranjang (cart)
            foreach ($validated['cart'] as $item) {
                $produk_id = $item['produk_id'];
                $qty = $item['qty'];

                $hargaJualTerbaru = PenerimaanBarang::where('produk_id', $produk_id)
                    ->where('qty', '>', 0)
                    ->orderBy('tgl_masuk', 'desc') // Urutkan dari tanggal terbaru
                    ->orderBy('id', 'desc') // Urutkan dari yang terakhir diinput kalau tanggalnya sama
                    ->first()->harga_jual ?? 0;

                // Ambil stok dari penerimaan_barang berdasarkan FIFO
                $stok_tersedia = PenerimaanBarang::where('produk_id', $produk_id)
                    ->where('qty', '>', 0)
                    ->orderBy('tgl_masuk', 'asc')
                    ->get();

                if ($stok_tersedia->isEmpty()) {
                    Log::warning("Stok produk habis!", ['produk_id' => $produk_id]);
                    throw new \Exception("Stok produk ID $produk_id habis!");
                }

                foreach ($stok_tersedia as $stok) {
                    if ($qty <= 0)
                        break;

                    $ambilQty = min($qty, $stok->qty);
                    $subtotal = $ambilQty * $hargaJualTerbaru; // Gunakan harga terbaru

                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'penerimaan_barang_id' => $stok->id,
                        'produk_id' => $produk_id,
                        'qty' => $ambilQty,
                        'harga_jual' => $hargaJualTerbaru, // Pakai harga terbaru
                        'sub_total' => $subtotal,
                    ]);

                    Log::info('Detail penjualan berhasil disimpan', [
                        'penjualan_id' => $penjualan->id,
                        'produk_id' => $produk_id,
                        'qty' => $ambilQty,
                        'harga_jual' => $hargaJualTerbaru,
                    ]);

                    // Simpan perubahan stok ke ActivityLog
                    ActivityLog::create([
                        'action' => 'update',
                        'description' => 'Stok produk berkurang setelah penjualan',
                        'data' => json_encode([
                            'produk_id' => $produk_id,
                            'stok_sebelum' => $stok->qty,
                            'stok_dikurangi' => $ambilQty,
                            'stok_setelah' => $stok->qty - $ambilQty
                        ])
                    ]);

                    // Kurangi stok
                    $stok->qty -= $ambilQty;
                    $stok->save();

                    $totalBayar += $subtotal;
                    $qty -= $ambilQty;
                }

            }

            // Update total bayar di tabel penjualan
            $penjualan->update(['total_bayar' => $totalBayar]);

            Log::info('Total bayar diperbarui', ['penjualan_id' => $penjualan->id, 'total_bayar' => $totalBayar]);

            // Simpan perubahan total bayar ke ActivityLog
            ActivityLog::create([
                'action' => 'update',
                'description' => 'Total bayar diperbarui setelah transaksi selesai',
                'data' => json_encode([
                    'penjualan_id' => $penjualan->id,
                    'total_bayar' => $totalBayar
                ])
            ]);

            DB::commit();

            return redirect()->route('pembayaran.create', [
                'penjualan' => $penjualan->id,
                'success' => 'Penjualan berhasil ditambah, silakan lanjutkan pembayaran'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menyimpan penjualan: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return redirect()->route('penjualan.create')->with('error', 'Penjualan gagal ditambah: ' . $th->getMessage());
        }
    }

    public function create(Request $request)
    {
        Log::info('Mengakses halaman pembuatan penjualan', ['user_id' => Auth::id()]);

        $search = $request->input('search');
        if ($search) {
            Log::info('User melakukan pencarian produk', ['search' => $search]);
        }

        $members = Member::all();
        $produk = Produk::whereHas('penerimaanBarang', function ($query) {
            $query->havingRaw('SUM(qty) > 0');
        })
            ->with([
                'penerimaanBarang' => function ($query) {
                    $query->select('produk_id', 'qty', 'harga_jual');
                }
            ])
            ->when($search, function ($query) use ($search) {
                return $query->where('nama_barang', 'like', "%$search%");
            })
            ->paginate(4)
            ->appends(['search' => $search]);

        Log::info('Produk yang ditemukan', ['jumlah_produk' => $produk->total()]);

        return view('penjualan.create', compact('members', 'produk', 'search'));
    }

    public function show($id)
    {
        Log::info('Mengakses halaman detail penjualan', ['user_id' => Auth::id(), 'penjualan_id' => $id]);

        $penjualan = Penjualan::with(['detailPenjualan.produk', 'user', 'member'])->find($id);

        if (!$penjualan) {
            Log::warning('Penjualan tidak ditemukan', ['penjualan_id' => $id]);

            // Simpan ke ActivityLog bahwa data tidak ditemukan
            ActivityLog::create([
                'action' => 'view_failed',
                'description' => 'Gagal mengakses detail penjualan - Data tidak ditemukan',
                'data' => json_encode(['penjualan_id' => $id, 'user_id' => Auth::id()])
            ]);

            return redirect()->route('penjualan.index')->with('error', 'Data penjualan tidak ditemukan.');
        }

        Log::info('Detail penjualan ditemukan', ['penjualan_id' => $id]);

        // Simpan ke ActivityLog bahwa user melihat detail penjualan
        ActivityLog::create([
            'action' => 'view',
            'description' => 'Melihat detail penjualan',
            'data' => json_encode([
                'penjualan_id' => $id,
                'user_id' => Auth::id()
            ])
        ]);

        return view('penjualan.detail', compact('penjualan'));
    }


}
