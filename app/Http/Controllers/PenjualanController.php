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

// Controller utama untuk mengatur logika terkait penjualan 
class PenjualanController extends Controller
{
    /**
     * Menampilkan halaman daftar penjualan.
     * Mengambil data penjualan beserta relasi user dan member.
     */
    public function index()
    {
        // Catat log akses halaman daftar penjualan
        Log::info('Mengakses halaman daftar penjualan oleh user', ['user_id' => Auth::id()]);

        // Ambil seluruh data penjualan dengan relasi user dan member, urut dari terbaru
        $penjualan = Penjualan::with('user', 'member')->orderBy('created_at', 'desc')->get();
        Log::info('Data penjualan diambil', ['jumlah_penjualan' => $penjualan->count()]);

        // Ambil data semua member
        $members = Member::all();

        // Ambil produk dengan relasi penerimaanBarang (untuk mengambil harga jual terbaru)
        $produk = Produk::with([
            'penerimaanBarang' => function ($query) {
                $query->orderBy('tgl_masuk', 'desc'); // Urutkan harga jual dari yang terbaru
            }
        ])->get()->map(function ($p) {
            $p->harga_jual = $p->penerimaanBarang->first()->harga_jual ?? 0; // Ambil harga jual terbaru jika ada
            return $p;
        });

        // Tampilkan view penjualan.index dengan data penjualan, member, dan produk
        return view('penjualan.index', compact('penjualan', 'members', 'produk'));
    }

    /**
     * Menyimpan data transaksi penjualan baru ke dalam database.
     */
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

                // Jika tidak ada stok, batalkan transaksi
                if ($stok_tersedia->isEmpty()) {
                    Log::warning("Stok produk habis!", ['produk_id' => $produk_id]);
                    throw new \Exception("Stok produk ID $produk_id habis!");
                }

                // Ambil stok dari tabel penerimaan sampai kebutuhan qty terpenuhi
                foreach ($stok_tersedia as $stok) {
                    if ($qty <= 0)
                        break;

                    $ambilQty = min($qty, $stok->qty);
                    $subtotal = $ambilQty * $hargaJualTerbaru;

                    // Simpan detail transaksi
                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'penerimaan_barang_id' => $stok->id,
                        'produk_id' => $produk_id,
                        'qty' => $ambilQty,
                        'harga_jual' => $hargaJualTerbaru,
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

                    // Update stok
                    $stok->qty -= $ambilQty;
                    $stok->save();

                    $totalBayar += $subtotal;
                    $qty -= $ambilQty;
                }

            }

            // Update total pembayaran penjualan
            $penjualan->update(['total_bayar' => $totalBayar]);

            Log::info('Total bayar diperbarui', ['penjualan_id' => $penjualan->id, 'total_bayar' => $totalBayar]);

            // Log perubahan total bayar
            ActivityLog::create([
                'action' => 'update',
                'description' => 'Total bayar diperbarui setelah transaksi selesai',
                'data' => json_encode([
                    'penjualan_id' => $penjualan->id,
                    'total_bayar' => $totalBayar
                ])
            ]);

            DB::commit();

            // Redirect ke halaman pembayaran tanpa menambahkan session 'success'
            return redirect()->route('pembayaran.create', [
                'penjualan' => $penjualan->id
            ]);

            // print_r("berhasil");

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menyimpan penjualan: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return redirect()->route('penjualan.create')->with('error', 'Penjualan gagal ditambah: ' . $th->getMessage());
            // print_r("gagal");
            // print_r($th->getMessage());
        }
    }

    /**
     * Menampilkan form untuk membuat transaksi penjualan baru.
     */
    public function create(Request $request)
    {
        $search = $request->input('search');
    
        $members = Member::all();
    
        // Produk untuk tampilan utama (paginate)
        $produk = Produk::whereHas('penerimaanBarang', function ($query) {
                $query->select('produk_id')
                    ->groupBy('produk_id')
                    ->havingRaw('SUM(qty) > 0');
            })
            ->with(['kategori', 'penerimaanBarang' => function($q) {
                $q->select('produk_id', 'qty', 'harga_jual', 'tgl_masuk')
                  ->orderBy('tgl_masuk', 'desc');
            }])
            ->when($search, function ($query, $search) {
                return $query->where('nama_barang', 'like', "%$search%")
                             ->orWhere('kode_barang', 'like', "%$search%");
            })
            ->paginate(12)
            ->appends(['search' => $search]);
    
        // Semua produk untuk scanning barcode - DIUBAH
        $dataProduk = Produk::whereHas('penerimaanBarang', function ($query) {
                $query->select('produk_id')
                    ->groupBy('produk_id')
                    ->havingRaw('SUM(qty) > 0');
            })
            ->with([
                'kategori',
                'penerimaanBarang' => function($q) {
                    $q->select('produk_id', 'qty', 'harga_jual', 'tgl_masuk')
                      ->orderBy('tgl_masuk', 'desc');
                }
            ])
            ->get()
            ->map(function ($item) {
                $totalStok = $item->penerimaanBarang->sum('qty');
                $latestReceipt = $item->penerimaanBarang->first();
                
                return [
                    'id' => $item->id,
                    'kode_barang' => $item->kode_barang,
                    'nama_barang' => $item->nama_barang,
                    'harga_jual' => $latestReceipt ? $latestReceipt->harga_jual : 0,
                    'stok' => $totalStok,
                    'gambar' => $item->gambar,
                    'kategori' => $item->kategori
                ];
            });
    
        return view('penjualan.create', compact('members', 'produk', 'search', 'dataProduk'));
    }

    /**
     * Menampilkan detail penjualan berdasarkan ID tertentu.
     */
    public function show($id)
    {
        Log::info('Mengakses halaman detail penjualan', ['user_id' => Auth::id(), 'penjualan_id' => $id]);

        // Ambil penjualan berdasarkan ID beserta relasinya
        $penjualan = Penjualan::with(['detailPenjualan.produk', 'user', 'member'])->find($id);

        // Jika data tidak ditemukan, redirect dengan error
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