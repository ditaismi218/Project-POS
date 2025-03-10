@extends('layouts.layout')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h5 class="card-header text-md-start text-center">History Pembayaran</h5>
        <div class="card-datatable">
            <table class="dt-scrollableTable table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Penjualan</th>
                        <th>Subtotal</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembayaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->penjualan->no_faktur }}</td>
                            <td>Rp {{ number_format($item->penjualan->total_bayar, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->kembalian, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($item->metode_pembayaran) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });

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
                    }, ],
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
