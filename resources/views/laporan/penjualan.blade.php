@extends('layouts.layout')

@section('content')
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="m-0">Laporan Total Penjualan Barang</h5>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <!-- Dropdown Export -->
                {{-- <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </button>
                    <ul class="dropdown-menu" id="export-menu" aria-labelledby="exportDropdown"></ul>
                </div>  --}}

                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownExportButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-download"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownExportButton">
                        <li>
                            <!-- Link untuk Export PDF -->
                            <a class="dropdown-item"
                                href="{{ route('laporan.penjualan', ['export_pdf' => 1, 'kategori' => request('kategori')]) }}">
                                <i class="fa fa-file-pdf text-danger me-2"></i> Export PDF
                            </a>
                        </li>
                        <li>
                            <!-- Link untuk Export Excel -->
                            <a class="dropdown-item"
                                href="{{ route('laporan.penjualan.export', ['kategori' => request('kategori')]) }}">
                                <i class="fa fa-file-excel text-success me-2"></i> Export Excel
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="d-flex gap-2">
                    <select name="kategori" class="form-control form-control-sm w-auto">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-secondary btn-sm">Reset</a>
                </form>
            </div>
        </div>

        <div class="card-datatable pb-3 px-3">
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
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            let a = document.querySelector(".dt-scrollableTable");
            a &&
                new DataTable(a, {
                    columnDefs: [{
                            targets: -2,
                            render: function(e, t, a, s) {
                                var a = a.status,
                                    r = {
                                        1: {
                                            title: "Current",
                                            class: "bg-label-primary",
                                        },
                                        2: {
                                            title: "Professional",
                                            class: "bg-label-success",
                                        },
                                        3: {
                                            title: "Rejected",
                                            class: "bg-label-danger",
                                        },
                                        4: {
                                            title: "Resigned",
                                            class: "bg-label-warning",
                                        },
                                        5: {
                                            title: "Applied",
                                            class: "bg-label-info"
                                        },
                                    };
                                return void 0 === r[a] ?
                                    e :
                                    `
                <span class="badge ${r[a].class}">
                    ${r[a].title}
                </span>
                `;
                            },
                        },
                        // {
                        // targets: -1,
                        // title: "Actions",
                        // searchable: !1,
                        // className: "d-flex align-items-center",
                        // orderable: !1,
                        // render: function (e, t, a, s) {
                        //     return '<div class="d-inline-block"><a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded icon-base"></i></a><div class="dropdown-menu dropdown-menu-end m-0"><a href="javascript:;" class="dropdown-item">Details</a><a href="javascript:;" class="dropdown-item">Archive</a><div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></div></div><a href="javascript:;" class="item-edit text-body"><i class="bx bxs-edit icon-base"></i></a>';
                        // },
                        // },
                    ],
                    // scrollY: "300px",
                    scrollX: !0,
                    layout: {
                        topStart: {
                            rowClass: "row mx-3 my-0 justify-content-between",
                            features: [{
                                pageLength: {
                                    menu: [7, 10, 25, 50, 100],
                                    text: "Show_MENU_entries",
                                },
                            }, ],
                        },
                        topEnd: {
                            search: {
                                placeholder: ""
                            }
                        },
                        bottomStart: {
                            rowClass: "row mx-3 justify-content-between",
                            features: ["info"],
                        },
                        bottomEnd: {
                            paging: {
                                firstLast: !1
                            }
                        },
                    },
                    language: {
                        paginate: {
                            next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                            previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                        },
                    },
                    initComplete: function(e, t) {
                        a.querySelector("tbody tr:first-child").classList.add(
                            "border-top-0"
                        );
                    },
                });

        });
    </script>
@endpush
