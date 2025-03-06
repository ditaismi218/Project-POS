@extends('layouts.layout')
@section('title', 'Member')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Member
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Member</h5>
            <div class="card-datatable">
            {{-- <div class="card-datatable overflow-auto"> --}}
                <table class="table table-bordered dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Member</th>
                            <th>Nama</th>
                            <th>No Telepon</th>
                            <th>Alamat</th>
                            <th>Loyalty Points</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($member as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_member }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->no_telp }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->loyalty_points ?? 0 }}</td>
                                <td>{{ $item->tgl_bergabung }}</td>
                                <td>
                                    <button class="btn btn-warning"
                                        onclick="test('{{ $item->id }}', '{{ $item->kode_member }}', '{{ $item->nama }}', '{{ $item->no_telp }}', '{{ $item->alamat }}', '{{ $item->loyalty_points }}', '{{ $item->tgl_bergabung }}')"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>
                
                                    <button class="btn btn-danger delete-button" 
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                
                                    <form id="delete-form-{{ $item->id }}" 
                                        action="{{ route('member.destroy', $item->id) }}" 
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
        </div>

    </div>

@endsection

    {{-- modal tambah --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        {{-- <div class="mb-3">
                            <label for="kode_member" class="form-label">Kode Member</label>
                            <input type="text" class="form-control" id="kode_member" name="kode_member" required>
                        </div> --}}

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_telp" class="form-label" >No Telp</label>
                            <input type="number" class="form-control" id="no_telp" name="no_telp" maxlength="12" 
                            oninput="this.value = this.value.slice(0, 12)" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
                        </div>                       

                        <div class="mb-3">
                            <label for="loyalty_points" class="form-label">Loyalty Points</label>
                            <input type="number" class="form-control" id="loyalty_points" name="loyalty_points" value="0" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="tgl_bergabung" class="form-label">Tanggal Bergabung</label>
                            <input type="date" class="form-control" id="tgl_bergabung" name="tgl_bergabung" value="0" min="0">
                        </div>

                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

     
<!-- Modal Edit Produk -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT') <!-- Tambahkan ini agar Laravel mengenali metode PUT -->
                    
                    <input type="hidden" id="id" name="id">
                
                    <div class="mb-3">
                        <label for="kode_member-edit" class="form-label">Kode Member</label>
                        <input type="text" class="form-control" id="kode_member-edit" name="kode_member" readonly>
                    </div>                    

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama-edit" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_telp" class="form-label" >No Telp</label>
                        <input type="number" class="form-control" id="no_telp-edit" name="no_telp" maxlength="12" 
                        oninput="this.value = this.value.slice(0, 12)" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat-edit" name="alamat" rows="2" required></textarea>
                    </div>                       

                    <div class="mb-3">
                        <label for="loyalty_points" class="form-label">Loyalty Points</label>
                        <input type="number" class="form-control" id="loyalty_points-edit" name="loyalty_points" value="0" min="0">
                    </div>
                
                    <div class="mb-3">
                        <label for="tgl_bergabung" class="form-label">Tanggal Bergabung</label>
                        <input type="date" class="form-control" id="tgl_bergabung-edit" name="tgl_bergabung" value="0" min="0">
                    </div>
                
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
                
            </div>
        </div>
    </div>
</div>

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


            document.addEventListener('DOMContentLoaded', function() {
                let productModal = new bootstrap.Modal(document.getElementById('productModal'));

                // Event untuk Tambah Supplier
                document.getElementById('createProductButton').addEventListener('click', function() {
                    let modal = new bootstrap.Modal(document.getElementById('productModal'));
                    modal.show();
                });

                // Event untuk Hapus User
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', function() {
                        
                        let id = this.getAttribute('data-id');
                        let nama = this.getAttribute('data-nama');

                        Swal.fire({
                            title: "Apakah Anda yakin?",
                            text: `Member "${nama}" akan dihapus secara permanen!`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Ya, hapus!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`delete-form-${id}`).submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
