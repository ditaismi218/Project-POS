<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\PenerimaanBarang;
use App\Models\Produk;
use App\Models\VoucherDiskon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with('user', 'member', 'voucher')->orderBy('created_at', 'desc')->get();
        $members = Member::all();
        $produk = Produk::with(['penerimaanBarang' => function ($query) {
            $query->orderBy('tgl_masuk', 'desc'); // Ambil harga terbaru berdasarkan FIFO
        }])->get();
        $vouchers = VoucherDiskon::all();

        return view('penjualan.index', compact('penjualan', 'members', 'vouchers', 'produk'));
    }

    // public function store(Request $request)
    // {
    //     // Debug untuk melihat data request yang diterima
    //     dd($request->all());

    //     $request->validate([
    //         'member_id' => 'nullable|exists:members,id',
    //         'voucher_id' => 'nullable|exists:voucher_diskons,id',
    //         'status' => 'required|string',
    //         'cart' => 'required|array', // Sesuaikan dengan struktur array cart
    //         'cart.*.produk_id' => 'required|exists:produk,id',
    //         'cart.*.qty' => 'required|integer|min:1',
    //     ]);

    //     try {
    //         DB::beginTransaction();
    //         $totalBayar = 0;

    //         $penjualan = Penjualan::create([
    //             'user_id' => Auth::id(),
    //             'member_id' => $request->member_id,
    //             'voucher_id' => $request->voucher_id,
    //             'tgl_faktur' => now(),
    //             'total_bayar' => 0, // Akan dihitung ulang nanti
    //             'status' => $request->status,
    //         ]);

    //         foreach ($request->cart as $item) {
    //             $produk_id = $item['produk_id'];
    //             $qty = $item['qty'];

    //             // Ambil stok dari penerimaan_barang (FIFO)
    //             $stok_tersedia = PenerimaanBarang::where('produk_id', $produk_id)
    //                 ->where('qty', '>', 0)
    //                 ->orderBy('tgl_masuk', 'asc')
    //                 ->get();

    //             if ($stok_tersedia->isEmpty()) {
    //                 throw new \Exception("Stok produk ID $produk_id habis!");
    //             }

    //             foreach ($stok_tersedia as $stok) {
    //                 if ($qty <= 0) break;

    //                 $ambilQty = min($qty, $stok->qty);
    //                 $hargaJual = $stok->harga_jual;
    //                 $subtotal = $ambilQty * $hargaJual;

    //                 DetailPenjualan::create([
    //                     'penjualan_id' => $penjualan->id,
    //                     'penerimaan_barang_id' => $stok->id,
    //                     'produk_id' => $produk_id,
    //                     'qty' => $ambilQty,
    //                     'harga_jual' => $hargaJual, // Simpan harga jual
    //                     'sub_total' => $subtotal,
    //                 ]);

    //                 $stok->qty -= $ambilQty;
    //                 $stok->save();
    //                 $totalBayar += $subtotal;
    //                 $qty -= $ambilQty;
    //             }
    //         }

    //         // Hitung diskon jika ada
    //         $diskon = 0;
    //         if ($request->voucher_id) {
    //             $voucher = VoucherDiskon::find($request->voucher_id);
    //             if ($voucher) {
    //                 $diskon = $voucher->nilai;
    //             }
    //         }

    //         $totalBayar = max(0, $totalBayar - $diskon);

    //         // Update total bayar di tabel penjualan
    //         $penjualan->update(['total_bayar' => $totalBayar]);

    //         DB::commit();
    //         return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Error saat menyimpan penjualan: " . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan.');
    //     }
    // }

    public function store(Request $request)
    {
        // Validasi data
        // dd($request->all());
        $validated = $request->validate([
            'member_id' => 'nullable|exists:member,id',
            'voucher_id' => 'nullable|exists:voucher,id',
            'status' => 'required|in:lunas,belum_lunas,batal,pending',
            'cart' => 'required|array',
            'cart.*.produk_id' => 'required|exists:produk,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalBayar = 0;
            $tgl_faktur = now();

            // Buat transaksi penjualan baru
            $penjualan = Penjualan::create([
                'user_id' => Auth::id(),
                'member_id' => $validated['member_id'] ?? null,
                'voucher_id' => $validated['voucher_id'] ?? null,
                'tgl_faktur' => $tgl_faktur,
                'total_bayar' => 0,
                'status' => $validated['status'],
            ]);

            // Proses setiap item dalam keranjang (cart)
            foreach ($validated['cart'] as $item) {
                $produk_id = $item['produk_id'];
                $qty = $item['qty'];

                // Ambil stok dari penerimaan_barang berdasarkan FIFO (stok masuk lebih awal dijual lebih dulu)
                $stok_tersedia = PenerimaanBarang::where('produk_id', $produk_id)
                    ->where('qty', '>', 0)
                    ->orderBy('tgl_masuk', 'asc')
                    ->get();

                if ($stok_tersedia->isEmpty()) {
                    throw new \Exception("Stok produk ID $produk_id habis!");
                }

                foreach ($stok_tersedia as $stok) {
                    if ($qty <= 0) break;

                    $ambilQty = min($qty, $stok->qty);
                    $hargaJual = $stok->harga_jual; // Ambil harga dari penerimaan_barang
                    $subtotal = $ambilQty * $hargaJual;

                    // Simpan detail penjualan (tanpa menyimpan harga_jual)
                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'penerimaan_barang_id' => $stok->id,
                        'produk_id' => $produk_id,
                        'qty' => $ambilQty,
                        'sub_total' => $subtotal,
                    ]);

                    // Kurangi stok di penerimaan_barang
                    $stok->qty -= $ambilQty;
                    $stok->save();

                    // Tambahkan subtotal ke total bayar
                    $totalBayar += $subtotal;
                    $qty -= $ambilQty;
                }
            }

            // Update total bayar di tabel penjualan
            $penjualan->update(['total_bayar' => $totalBayar]);

            DB::commit();
            // return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambah');
            return redirect()->route('pembayaran.create', ['penjualan' => $penjualan->id])
            ->with('success', 'Penjualan berhasil ditambah, silakan lanjutkan pembayaran');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return redirect()->route('penjualan.create')->with('error', 'Penjualan gagal ditambah: ' . $th->getMessage());
        }
    }

    public function create(Request $request)
    {
        $search = $request->input('search'); // Ambil input pencarian
    
        $members = Member::all();
        $vouchers = VoucherDiskon::all();
    
        // Query produk dengan pencarian jika ada
        $produk = Produk::with('penerimaanBarang')
            ->when($search, function ($query) use ($search) {
                return $query->where('nama_barang', 'like', "%$search%");
            })
            ->paginate(8)
            ->appends(['search' => $search]); // Menjaga query search di pagination
    
        return view('penjualan.create', compact('members', 'vouchers', 'produk', 'search'));
    }    
    
    public function show($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.produk', 'user', 'member'])
            ->findOrFail($id); // Gunakan findOrFail agar lebih aman

        return view('penjualan.detail', compact('penjualan'));
    }

    
}
