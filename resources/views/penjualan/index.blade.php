@extends('layouts.layout')

@section('content')
    <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-5">Tambah Penjualan</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <h5 class="card-header text-md-start text-center">Data Penjualan</h5>
        <div class="card-datatable">
            <table class="table table-striped dt-scrollableTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Faktur</th>
                        <th>Pelanggan</th>
                        <th>Subtotal</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $key => $p)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $p->no_faktur }}</td>
                            <td>{{ $p->member->nama ?? 'Umum' }}</td>
                            <td>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($p->status) }}</td>
                            <td>{{ $p->created_at->format('d-m-Y') }}</td>
                            <td>
                                <!-- Tombol Detail -->
                                <a href="{{ route('penjualan.show', $p->id) }}"
                                    class="btn btn-info flex items-center justify-center h-12 px-4 rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:outline-none transition duration-300 ease-in-out">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Tombol Create Pembayaran dengan Ikon -->
                                <a href="{{ route('pembayaran.create', $p->id) }}"
                                    class="btn btn-primary flex items-center justify-center h-12 px-4 rounded-md text-white bg-purple-400 hover:bg-purple-500 focus:ring-2 focus:ring-purple-300 focus:outline-none transition duration-300 ease-in-out"
                                    @if ($p->status == 'lunas') disabled style="pointer-events: none; opacity: 0.5;" @endif>
                                    <i class="bx bx-credit-card text-xl"></i>
                                </a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada transaksi penjualan</td>
                        </tr>
                    @endforelse
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
