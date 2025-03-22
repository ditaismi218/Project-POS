@extends('layouts.layout')

@section('content')
<div class="page-body">
    <div class="card">
        <h5 class="card-header text-md-start text-center">Tabel Logs</h5>
        <div class="card-datatable">
            <table class="table table-striped dt-scrollableTable">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>Data</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ json_encode($log->data) }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });

    function test(id, kode_member, nama, no_telp, alamat, loyalty_points, tgl_bergabung) {
    document.getElementById('id').value = id;
    document.getElementById('kode_member-edit').value = kode_member; // Tetap bisa diisi tetapi tidak bisa diedit
    document.getElementById('nama-edit').value = nama;
    document.getElementById('no_telp-edit').value = no_telp;
    document.getElementById('alamat-edit').value = alamat;
    document.getElementById('loyalty_points-edit').value = loyalty_points;
    document.getElementById('tgl_bergabung-edit').value = tgl_bergabung;

    // Atur form agar mengirim ke /member/{id} dengan metode PUT
    document.getElementById('editForm').action = "/member/" + id;
}

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- JavaScript -->

<script>
    document.addEventListener("DOMContentLoaded", function (e) {
        let a = document.querySelector(".dt-scrollableTable");
        a &&
            new DataTable(a, {
                columnDefs: [
                    {
                        targets: -2,
                        render: function (e, t, a, s) {
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
                                    5: { title: "Applied", class: "bg-label-info" },
                                };
                            return void 0 === r[a]
                                ? e
                                : `
                <span class="badge ${r[a].class}">
                    ${r[a].title}
                </span>
                `;
                        },
                    },
                ],
                // scrollY: "300px",
                scrollX: !0,
                layout: {
                    topStart: {
                        rowClass: "row mx-3 my-0 justify-content-between",
                        features: [
                            {
                                pageLength: {
                                    menu: [7, 10, 25, 50, 100],
                                    text: "Show_MENU_entries",
                                },
                            },
                        ],
                    },
                    topEnd: { search: { placeholder: "" } },
                    bottomStart: {
                        rowClass: "row mx-3 justify-content-between",
                        features: ["info"],
                    },
                    bottomEnd: { paging: { firstLast: !1 } },
                },
                language: {
                    paginate: {
                        next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                        previous:
                            '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                    },
                },
                initComplete: function (e, t) {
                    a.querySelector("tbody tr:first-child").classList.add(
                        "border-top-0"
                    );
                },
            });
        
    });


    
</script>
@endpush
