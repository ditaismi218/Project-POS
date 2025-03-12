@extends('layouts.layout')
@section('title', 'Penerimaan Barang')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Supplier
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Supplier</h5>
            <div class="card-datatable">
                <table class="table table-striped dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Supplier</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplier as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_supplier }}</td>
                                <td>{{ $item->telepon }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_supplier }}" data-telepon="{{ $item->telepon }}"
                                        data-email="{{ $item->email }}" data-alamat="{{ $item->alamat }}"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_supplier }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('supplier.destroy', $item->id) }}" method="POST"
                                        style="display:none;">
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


    {{-- modal --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="number" class="form-control" id="telepon" name="telepon" required maxlength="12"
                                oninput="this.value = this.value.slice(0, 12)">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                        </div>

                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });

        document.addEventListener("DOMContentLoaded", function() {
            let a = document.querySelector(".dt-scrollableTable");
            if (a) {
                new DataTable(a, {
                    columnDefs: [{
                        targets: -2,
                        render: function(e, t, a, s) {
                            var r = {
                                1: { title: "Current", class: "bg-label-primary" },
                                2: { title: "Professional", class: "bg-label-success" },
                                3: { title: "Rejected", class: "bg-label-danger" },
                                4: { title: "Resigned", class: "bg-label-warning" },
                                5: { title: "Applied", class: "bg-label-info" },
                            };
                            return r[a] ? `<span class="badge ${r[a].class}">${r[a].title}</span>` : e;
                        },
                    }],
                    scrollX: true,
                    layout: {
                        topStart: {
                            rowClass: "row mx-3 my-0 justify-content-between",
                            features: [{ pageLength: { menu: [7, 10, 25, 50, 100], text: "Show_MENU_entries" } }],
                        },
                        topEnd: { search: { placeholder: "" } },
                        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
                        bottomEnd: { paging: { firstLast: false } },
                    },
                    language: {
                        paginate: {
                            next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                            previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                        },
                    },
                    initComplete: function() {
                        a.querySelector("tbody tr:first-child").classList.add("border-top-0");
                    },
                });
            }

            let productModal = new bootstrap.Modal(document.getElementById('productModal'));

            // Tambah Supplier
            document.getElementById('createProductButton').addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = "Tambah Supplier";
                document.getElementById('productForm').setAttribute('action', "{{ route('supplier.store') }}");
                document.getElementById('method').value = "POST";
                document.getElementById('submitButton').innerText = "Simpan";

                document.getElementById('nama_supplier').value = "";
                document.getElementById('telepon').value = "";
                document.getElementById('email').value = "";
                document.getElementById('alamat').value = "";

                productModal.show();
            });

            // Edit Supplier
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let nama = this.getAttribute('data-nama');
                    let telepon = this.getAttribute('data-telepon');
                    let email = this.getAttribute('data-email');
                    let alamat = this.getAttribute('data-alamat');

                    document.getElementById('modalTitle').innerText = "Edit Supplier";
                    document.getElementById('productForm').setAttribute('action', `{{ url('supplier') }}/${id}`);
                    document.getElementById('method').value = "PUT";
                    document.getElementById('submitButton').innerText = "Update";

                    document.getElementById('nama_supplier').value = nama;
                    document.getElementById('telepon').value = telepon;
                    document.getElementById('email').value = email;
                    document.getElementById('alamat').value = alamat;

                    productModal.show();
                });
            });

            // Hapus Supplier
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let nama = this.getAttribute('data-nama');

                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Supplier "${nama}" akan dihapus secara permanen!`,
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
