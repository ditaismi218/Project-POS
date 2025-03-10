@extends('layouts.layout')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0">Laporan Transaksi Barang</h5>
        <form method="GET" action="{{ route('laporan.transaksi') }}" class="d-flex gap-2">
            <input type="date" name="tanggal" class="form-control w-auto" value="{{ request('tanggal') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('laporan.transaksi') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>     
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
