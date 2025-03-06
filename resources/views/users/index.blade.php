@extends('layouts.layout')
@section('title', 'Manajemen Produk')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah User
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel User</h5>
            <div class="card-datatable">
            {{-- <div class="card-datatable overflow-auto"> --}}
                <table class="table table-bordered dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->password }}</td> 
                                <td>{{ $item->role }}</td>
                                <td>
                                    <button class="btn btn-warning"
                                        onclick="test('{{ $item->id }}', '{{ $item->name }}', '{{ $item->email }}', '{{ $item->password }}', '{{ $item->role }}')"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" 
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->name }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}" 
                                        action="{{ route('users.destroy', $item->id) }}" 
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
                    <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama User</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>                        

                        <div class="mb-3">
                            <label for="role" class="form-label">role</label>
                            <select name="role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                                <option value="gudang">Gudang</option>
                            </select>
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
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT') <!-- Tambahkan ini agar Laravel mengenali metode PUT -->
                    
                    <input type="hidden" id="id" name="id">
                
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama User</label>
                        <input type="text" class="form-control" id="name-edit" name="name" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email-edit" name="email" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" id="password-edit" name="password">
                    </div>
                
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-control" id="role-edit" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="gudang">Gudang</option>
                        </select>
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

            function test(id, name, email, password, role) {
            document.getElementById('id').value = id;
            document.getElementById('name-edit').value = name;
            document.getElementById('email-edit').value = email;
            document.getElementById('password-edit').value = password;
            document.getElementById('role-edit').value = role;

            // Atur form agar mengirim ke /users/{id} dengan metode PUT
    document.getElementById('editForm').action = "/users/" + id;
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
                            text: `User "${nama}" akan dihapus secara permanen!`,
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
