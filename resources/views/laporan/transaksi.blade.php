@extends('layouts.layout')

@section('content')
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white shadow-sm p-3 rounded mb-5">
        <h5 class="m-0">Laporan Transaksi Penjualan Barang</h5>
        <div class="d-flex flex-wrap gap-2 ms-auto align-items-center">
            <!-- Form Filter -->
            <form method="GET" action="{{ route('laporan.transaksi') }}" class="d-flex flex-wrap gap-2">
                <input type="date" name="tanggal_mulai" class="form-control form-control-sm w-auto"
                    value="{{ request('tanggal_mulai') }}">
                <input type="date" name="tanggal_selesai" class="form-control form-control-sm w-auto"
                    value="{{ request('tanggal_selesai') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bx bx-filter"></i>
                </button>
                <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary btn-sm">
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
        <div class="card-datatable">
            <table class="dt-scrollableTable table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Produk Dibeli</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->no_faktur }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_faktur)->format('d-m-Y') }}</td>
                            <td>{{ $item->member->nama ?? 'Umum' }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>
                                <a href="{{ route('transaksi.detail', $item->id) }}" class="btn btn-primary btn-sm">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
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
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [0, 1, 2, 3,
                                4
                            ] // Jangan masukkan kolom "Produk Dibeli" (kolom ke-5)
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [0, 1, 2, 3,
                                4
                            ] // Jangan masukkan kolom "Produk Dibeli" (kolom ke-5)
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [0, 1, 2, 3,
                                4
                            ] // Jangan masukkan kolom "Produk Dibeli" (kolom ke-5)
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [0, 1, 2, 3,
                                4
                            ] // Jangan masukkan kolom "Produk Dibeli" (kolom ke-5)
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [0, 1, 2, 3,
                                4
                            ] // Jangan masukkan kolom "Produk Dibeli" (kolom ke-5)
                        }
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
