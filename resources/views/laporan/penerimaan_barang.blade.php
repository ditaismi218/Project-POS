@extends('layouts.layout')

@section('content')
    <div
        class="card-header d-flex flex-wrap justify-content-between align-items-center  bg-white shadow-sm p-3 rounded mb-5">
        <h5 class="m-0">Laporan Pembelian Barang</h5>
        <div class="d-flex flex-wrap gap-2 ms-auto align-items-center">
            <!-- Form Filter -->
            <form method="GET" action="{{ route('laporan.penerimaan_barang') }}" class="d-flex flex-wrap gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm w-auto"
                    value="{{ request('start_date') }}">
                <input type="date" name="end_date" class="form-control form-control-sm w-auto"
                    value="{{ request('end_date') }}">

                <select name="supplier_id" class="form-control form-control-sm w-auto">
                    <option value="">-- Semua Supplier --</option>
                    @foreach ($supplierList as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bx bx-filter"></i>
                </button>
                <a href="{{ route('laporan.penerimaan_barang') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-refresh"></i>
                </a>
            </form>

            <!-- Dropdown Export -->
            <div class="dropdown">
                <button type="button" class="btn btn-primary btn-sm py-2 px-2 dropdown-toggle" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-export"></i>
                </button>
                <ul class="dropdown-menu" id="export-menu" aria-labelledby="exportDropdown"></ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable pb-0 px-3">
            <table class=" table table-bordered dt-scrollableTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Penerimaan</th>
                        <th>Nama Produk</th>
                        <th>Supplier</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-center">Qty</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Expired Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_penerimaan }}</td>
                            <td>{{ $item->produk->nama_barang }}</td>
                            <td>{{ $item->supplier->nama_supplier }}</td>
                            <td>{{ $item->tgl_masuk }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td>{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->harga_total, 0, ',', '.') }}</td>
                            <td>{{ $item->expired_date ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total Keseluruhan:</td>
                        <td class="text-center">{{ $totalQty }}</td>
                        <td colspan="2" class="text-end">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('.dt-scrollableTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center px-3 py-2"' +
                    '<"col-auto"l>' +
                    '<"col-auto"f>' +
                    '>' +
                    'Brt<' +
                    '"d-flex justify-content-between align-items-center px-3 py-2"' +
                    '<"dataTables_info"i>' +
                    '<"dataTables_paginate"p>' +
                    '>',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'dropdown-item'
                    }
                ],
                scrollX: true,
                language: {
                    paginate: {
                        next: '<i class="bx bx-chevron-right icon-sm"></i>',
                        previous: '<i class="bx bx-chevron-left icon-sm"></i>',
                    },
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang sesuai",
                    emptyTable: "Tidak ada data tersedia",
                },
                lengthMenu: [7, 10, 25, 50, 100],
                pageLength: 10
            });

            table.buttons().container().find('button').each(function() {
                $('#export-menu').append($(this).wrap('<li>').parent());
            });
        });
    </script>
@endpush
