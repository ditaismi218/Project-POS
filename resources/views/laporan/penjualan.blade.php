@extends('layouts.layout')

@section('content')
    


    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="m-0">Laporan Penjualan Barang</h5>
    
            <div class="d-flex align-items-center gap-3 ms-auto">
                <!-- Dropdown Export -->
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </button>
                    <ul class="dropdown-menu" id="export-menu" aria-labelledby="exportDropdown"></ul>
                </div>
    
                <!-- Form Filter -->
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="d-flex gap-2">
                    <select name="kategori" class="form-control form-control-sm w-auto">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-secondary btn-sm">Reset</a>
                </form>
            </div>
        </div>

        <div class="card-datatable pb-0 px-3">
            <table class="dt-scrollableTable table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nama_kategori }}</td>
                            <td class="text-center">{{ $item->total_terjual }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">Total Keseluruhan:</td>
                        <td class="text-center">{{ $totalQty }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <!-- DataTables JS -->
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
                    '<"col-auto"l>' + // "Tampilkan _MENU_ data per halaman"
                    '<"col-auto"f>' + // "Cari:"
                    '>' +
                    'Brt<' +
                    '"d-flex justify-content-between align-items-center px-3 py-2"' +
                    '<"dataTables_info"i>' + // Teks info jumlah data
                    '<"dataTables_paginate"p>' + // Pagination
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
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang sesuai",
                    emptyTable: "Tidak ada data tersedia",
                },

                lengthMenu: [7, 10, 25, 50, 100],
                pageLength: 10
            });

            // Tempatkan tombol export ke dalam div #export-buttons
            table.buttons().container().find('button').each(function() {
                $('#export-menu').append($(this).wrap('<li>').parent());
            });
        });
    </script>
@endpush
