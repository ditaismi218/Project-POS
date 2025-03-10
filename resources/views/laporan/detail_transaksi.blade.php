@extends('layouts.layout')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Transaksi</h5>
    <div class="card-body">
        <div id="printArea">
            <table class="table">
                <tr>
                    <th>No Faktur</th>
                    <td>{{ $transaksi->no_faktur }}</td>
                </tr>
                <tr>
                    <th>Pelanggan</th>
                    <td>{{ $transaksi->member->nama ?? 'Umum' }}</td>
                </tr>
                <tr>
                    <th>Kasir</th>
                    <td>{{ $transaksi->user->name }}</td>
                </tr>
                <tr>
                    <th>Total Bayar</th>
                    <td>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Jumlah Bayar</th>
                    <td>Rp {{ number_format($transaksi->pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Kembalian</th>
                    <td>Rp {{ number_format($transaksi->pembayaran->kembalian ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Metode Pembayaran</th>
                    <td>{{ ucfirst($transaksi->pembayaran->metode_pembayaran ?? '-') }}</td>
                </tr>
            </table>

            <h5 class="mt-4">Produk Dibeli</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedProducts = [];
                    @endphp

                    @foreach ($transaksi->detailPenjualan as $detail)
                        @php
                            $namaProduk = $detail->produk->nama_barang;
                            if (!isset($groupedProducts[$namaProduk])) {
                                $groupedProducts[$namaProduk] = [
                                    'qty' => 0,
                                    'subtotal' => 0
                                ];
                            }
                            $groupedProducts[$namaProduk]['qty'] += $detail->qty;
                            $groupedProducts[$namaProduk]['subtotal'] += $detail->sub_total;
                        @endphp
                    @endforeach

                    @foreach ($groupedProducts as $namaProduk => $data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $namaProduk }}</td>
                        <td>{{ $data['qty'] }}</td>
                        <td>Rp {{ number_format($data['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button onclick="printDiv()" class="btn btn-primary mt-3">Print</button>
        <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>

<script>
    function printDiv() {
        var printContents = document.getElementById("printArea").innerHTML;
        var originalTitle = document.title; // Simpan title asli
        var invoiceNumber = "{{ $transaksi->no_faktur }}"; // Ambil no faktur dari Laravel

        document.title = "struk-" + invoiceNumber; // Set nama dokumen saat print
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        document.title = originalTitle; // Kembalikan title asli setelah print
        location.reload();
    }
</script>

@endsection
