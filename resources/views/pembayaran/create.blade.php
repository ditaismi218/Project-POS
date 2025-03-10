@extends('layouts.layout')

@section('content')

@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Pembayaran Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Lihat Detail',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ session('detail_url') }}";
            } else {
                window.location.href = "{{ route('pembayaran.index') }}"; // Redirect ke pembayaran.index
            }
        });
    </script>
@endif


<div class="card shadow-sm p-4">
    <!-- Header Pembayaran -->
    <div class="d-flex justify-content-between align-items-center mb-3 rounded">
        <h4 class="m-0"><i class="fas fa-receipt"></i> Pembayaran - No Faktur: {{ $penjualan->no_faktur }}</h4>
        <span class="badge bg-warning text-dark fw-bold">{{ ucfirst($penjualan->status) }}</span>
    </div>

    <!-- Informasi Pelanggan & Total Bayar -->
    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="fw-bold">Pelanggan:</h6>
            <input type="text" class="form-control bg-light" value="{{ $penjualan->member->nama ?? 'Pelanggan Lain' }}" readonly>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold">Total Bayar:</h6>
            <input type="text" class="form-control bg-light text-end fw-bold" 
                value="Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}" readonly>
        </div>
    </div>

    <!-- Detail Pembelian -->
    <h6 class="fw-bold">Detail Pembelian</h6>
    <div class="table-responsive mb-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga Jual Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedDetails = [];
                    foreach ($penjualan->detailPenjualan as $detail) {
                        $produkId = $detail->produk_id;
                        if (!isset($groupedDetails[$produkId])) {
                            $groupedDetails[$produkId] = [
                                'nama' => $detail->produk->nama_barang,
                                'jumlah' => 0,
                                'harga_jual' => $detail->produk->penerimaanBarang()->latest('tgl_masuk')->value('harga_jual'),
                            ];
                        }
                        $groupedDetails[$produkId]['jumlah'] += $detail->qty;
                    }
                @endphp

                @foreach ($groupedDetails as $detail)
                    <tr>
                        <td>{{ $detail['nama'] }}</td>
                        <td class="text-center">{{ $detail['jumlah'] }}</td>
                        <td class="text-end">Rp {{ number_format($detail['harga_jual'], 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($detail['jumlah'] * $detail['harga_jual'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Pembayaran -->
    <h5 class="fw-bold"><i class="fas fa-cash-register"></i> Form Pembayaran</h5>
    <form action="{{ route('pembayaran.store') }}" method="POST">
        @csrf
        <input type="hidden" name="penjualan_id" value="{{ $penjualan->id }}">

        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Jumlah Bayar:</label>
                <input type="number" id="jumlahBayar" name="jumlah_bayar" 
                    class="form-control" required oninput="hitungKembalian()">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Kembalian:</label>
                <input type="text" id="kembalian" class="form-control bg-light" readonly>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-bold">Metode Pembayaran:</label>
            <select name="metode_pembayaran" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="debit">Debit</option>
                <option value="kredit">Kredit</option>
                <option value="ewallet">E-Wallet</option>
            </select>
        </div>

        <!-- Tombol Bayar -->
        <button type="submit" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
            <i class="fas fa-check-circle"></i> Bayar Sekarang
        </button>
    </form>
</div>

<script>
    function hitungKembalian() {
        let totalBayar = {{ $penjualan->total_bayar }};
        let jumlahBayar = document.getElementById("jumlahBayar").value;

        if (jumlahBayar === '') {
            document.getElementById("kembalian").value = "";
            return;
        }

        let kembalian = jumlahBayar - totalBayar;
        document.getElementById("kembalian").value = kembalian >= 0 ? 
            `Rp ${new Intl.NumberFormat('id-ID').format(kembalian)}` : "Pembayaran kurang";
    }
</script>
@endsection
