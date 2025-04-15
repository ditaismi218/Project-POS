@extends('layouts.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="printArea" class="p-3">
                <!-- Header Toko -->
                <div class="text-center mb- mt-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bx bxs-store me-2" style="font-size: 32px;"></i>
                        <h5 class="fw-bold mb-1" style="font-size: 28px;">GoMart</h5>
                    </div>
                    <p class="mb-5">📞 0812-3456-7890 | 📍 Jl. Contoh No. 123, Kota</p>
                    <hr class="border border-dark mb-10">
                </div>

                <!-- Detail Transaksi -->
                <table class="table table-border">
                    <tr>
                        <td><strong>No Faktur</strong></td>
                        <td class="text-end">{{ $transaksi->no_faktur }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td class="text-end">{{ date('d-m-Y', strtotime($transaksi->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jam</strong></td>
                        <td class="text-end">{{ date('H:i:s', strtotime($transaksi->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pelanggan</strong></td>
                        <td class="text-end">{{ $transaksi->member->nama ?? 'Umum' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kasir</strong></td>
                        <td class="text-end">{{ $transaksi->user->name }}</td>
                    </tr>
                </table>
                <hr class="border border-dashed">

                <!-- Produk yang Dibeli -->
                <h6 class="text-center mb-2 fw-bold">🛒 Detail Pembelian</h6>
                <table class="table table-sm table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $groupedProducts = [];
                        @endphp

                        @foreach ($transaksi->detailPenjualan as $detail)
                            @php
                                $namaProduk = $detail->produk->nama_barang;
                                $hargaJual = $detail->harga_jual;

                                if (!isset($groupedProducts[$namaProduk])) {
                                    $groupedProducts[$namaProduk] = [
                                        'qty' => 0,
                                        'subtotal' => 0,
                                        'harga' => $hargaJual,
                                    ];
                                }

                                $groupedProducts[$namaProduk]['qty'] += $detail->qty;
                                $groupedProducts[$namaProduk]['subtotal'] += $detail->sub_total;
                            @endphp
                        @endforeach

                        @foreach ($groupedProducts as $namaProduk => $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $namaProduk }}</td>
                                <td class="text-end">Rp {{ number_format($data['harga'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ $data['qty'] }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($data['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <hr class="border border-dashed">

                <!-- Total Pembayaran -->
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Total Bayar</strong></td>
                        <td class="text-end fw-bold text-primary">Rp
                            {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Bayar</strong></td>
                        <td class="text-end">Rp {{ number_format($transaksi->pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Kembalian</strong></td>
                        <td class="text-end text-success">Rp
                            {{ number_format($transaksi->pembayaran->kembalian ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Metode Pembayaran</strong></td>
                        <td class="text-end">{{ ucfirst($transaksi->pembayaran->metode_pembayaran ?? '-') }}</td>
                    </tr>
                </table>
                <hr class="border border-dark">

                <div class="text-center mt-3">
                    <p><strong>🙏 Terima kasih telah berbelanja di GoMart! 🙌</strong></p>
                </div>
            </div>

            <div class="text-center mt-4">
                {{-- <button onclick="printDiv()" class="btn btn-primary btn-lg"><i class="fas fa-print"></i> Cetak Struk</button>
             --}}
                <a href="{{ route('struk.print', $transaksi->id) }}" class="btn btn-primary">
                    Cetak Struk
                </a>

                <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary btn-lg"><i
                        class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>

    <script>
        function printDiv() {
            var printContents = document.getElementById("printArea").innerHTML;
            var originalTitle = document.title;
            var invoiceNumber = "{{ $transaksi->no_faktur }}";

            document.title = "struk-" + invoiceNumber;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            document.title = originalTitle;
            location.reload();
        }
    </script>
@endsection
