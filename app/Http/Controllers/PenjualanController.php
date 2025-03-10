<?php

namespace App\Http\Controllers;

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
        $penjualan = Penjualan::with('user', 'member')->orderBy('created_at', 'desc')->get();
        $members = Member::all();
        $produk = Produk::with(['penerimaanBarang' => function ($query) {
            $query->orderBy('tgl_masuk', 'desc'); // Pastikan yang terbaru diambil
        }])->get()->map(function ($p) {
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

        // Proses setiap item dalam keranjang (cart)
        foreach ($validated['cart'] as $item) {
            $produk_id = $item['produk_id'];
            $qty = $item['qty'];

            // Ambil harga jual tertinggi dari stok yang tersedia
            $hargaJualTerbaru = PenerimaanBarang::where('produk_id', $produk_id)
            ->where('qty', '>', 0)
            ->orderBy('tgl_masuk', 'desc') // Ambil berdasarkan tanggal masuk terbaru
            ->first()->harga_jual ?? 0;        

            // Ambil stok dari penerimaan_barang berdasarkan FIFO
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

                // Pastikan harga jual tertinggi selalu dipakai
                $hargaJual = $hargaJualTerbaru ?: $stok->harga_jual;
                $subtotal = $ambilQty * $hargaJual;

                // Simpan detail penjualan dengan harga yang benar
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'penerimaan_barang_id' => $stok->id,
                    'produk_id' => $produk_id,
                    'qty' => $ambilQty,
                    'harga_jual' => $hargaJual, // Pastikan harga jual tersimpan
                    'sub_total' => $subtotal,
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

        DB::commit();

        return redirect()->route('pembayaran.create', [
            'penjualan' => $penjualan->id,
            'success' => 'Penjualan berhasil ditambah, silakan lanjutkan pembayaran'
        ]);
        
    } catch (\Throwable $th) {
        DB::rollBack();
        Log::error($th->getMessage());
        return redirect()->route('penjualan.create')->with('error', 'Penjualan gagal ditambah: ' . $th->getMessage());
    }
}

    public function create(Request $request)
    {
        $search = $request->input('search'); 
    
        $members = Member::all();    
        // Query produk dengan filter stok dan pencarian jika ada
        $produk = Produk::whereHas('penerimaanBarang', function ($query) {
                $query->havingRaw('SUM(qty) > 0'); // Hanya produk yang stoknya lebih dari 0
            })
            ->with(['penerimaanBarang' => function ($query) {
                $query->select('produk_id', 'qty', 'harga_jual');
            }])
            ->when($search, function ($query) use ($search) {
                return $query->where('nama_barang', 'like', "%$search%");
            })
            ->paginate(8)
            ->appends(['search' => $search]); // Menjaga query search di pagination
    
        return view('penjualan.create', compact('members', 'produk', 'search'));
    }       
    
    public function show($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.produk', 'user', 'member'])
            ->findOrFail($id); // Gunakan findOrFail agar lebih aman

        return view('penjualan.detail', compact('penjualan'));
    }

    
}
