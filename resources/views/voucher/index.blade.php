@extends('layouts.layout')
@section('title', 'Voucher')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Voucher
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Voucher</h5>
            <div class="card-datatable">
            {{-- <div class="card-datatable overflow-auto"> --}}
                <table class="table table-bordered dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Voucher</th>
                            <th>Jenis</th>
                            <th>Nilai</th>
                            <th>Minimal Belanja</th>
                            <th>Berlaku Hingga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voucher as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_voucher }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td>{{ $item->jenis == 'persentase' ? $item->nilai . '%' : 'Rp' . number_format($item->nilai, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->min_belanja, 0, ',', '.') }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->berlaku_hingga)) }}</td>
                                <td>
                                    <button class="btn btn-warning"
                                        onclick="test('{{ $item->id }}', '{{ $item->kode_voucher }}', '{{ $item->jenis }}', '{{ $item->nilai }}', '{{ $item->min_belanja }}', '{{ $item->berlaku_hingga }}')"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" 
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->kode_voucher }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}" 
                                        action="{{ route('voucher.destroy', $item->id) }}" 
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

                        <div class="mb-3">
                            <label for="kode_voucher" class="form-label">Kode Voucher</label>
                            <input type="text" class="form-control" id="kode_voucher" name="kode_voucher" required>
                        </div>

                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select name="jenis" class="form-control" required>
                                <option value="persentase">Persentase</option>
                                <option value="nominal">Nominal</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nilai" class="form-label">Nilai</label>
                            <input type="number" class="form-control" id="nilai" name="nilai" required>
                        </div>  

                        <div class="mb-3">
                            <label for="min_belanja" class="form-label">Minimal belanja</label>
                            <input type="number" class="form-control" id="min_belanja" name="min_belanja" required>
                        </div>                        

                        <div class="mb-3">
                            <label class="form-label">Berlaku Hingga</label>
                            <input type="date" name="berlaku_hingga" class="form-control" required>
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
                        <label for="kode_voucher" class="form-label">Kode Voucher</label>
                        <input type="text" class="form-control" id="kode_voucher-edit" name="kode_voucher" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select name="jenis" class="form-control" id="jenis-edit" required>
                            <option value="persentase">Persentase</option>
                            <option value="nominal">Nominal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai</label>
                        <input type="number" class="form-control" id="nilai-edit" name="nilai" required>
                    </div>  

                    <div class="mb-3">
                        <label for="min_belanja" class="form-label">Minimal belanja</label>
                        <input type="number" class="form-control" id="min_belanja-edit" name="min_belanja" required>
                    </div>                        

                    <div class="mb-3">
                        <label class="form-label">Berlaku Hingga</label>
                        <input type="date" name="berlaku_hingga" id="berlaku_hingga-edit" class="form-control" required>
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

            function test(id, kode_voucher, jenis, nilai, min_belanja, berlaku_hingga) {
            document.getElementById('id').value = id;
            document.getElementById('kode_voucher-edit').value = kode_voucher;
            document.getElementById('jenis-edit').value = jenis;
            document.getElementById('nilai-edit').value = nilai;
            document.getElementById('min_belanja-edit').value = min_belanja;
            document.getElementById('berlaku_hingga-edit').value = berlaku_hingga;

            // Atur form agar mengirim ke /users/{id} dengan metode PUT
            document.getElementById('editForm').action = "/voucher/" + id;
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
