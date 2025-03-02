@extends('layouts.layout')
@section('title', 'Manajemen Produk')

@section('content')
    <div class="page-body">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Produk
            </button>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr>
                                    <td>{{ $item->kode_barang }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->kategori->nama_kategori }}</td>
                                    <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        <button class="btn btn-warning edit-button" data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}"
                                            data-kategori="{{ $item->kategori_id }}" data-beli="{{ $item->harga_beli }}"
                                            data-jual="{{ $item->harga_jual }}" data-satuan="{{ $item->satuan }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                            Edit
                                        </button>

                                        <button class="btn btn-danger delete-button" data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_barang }}">
                                            Hapus
                                        </button>

                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('produk.destroy', $item->id) }}" method="POST"
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
    </div>


    {{-- modal --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        </div>

                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">Kategori</label>
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                @foreach ($categories as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="harga_beli" class="form-label">Harga Beli</label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" required>
                        </div>

                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan" required>
                        </div>

                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let productModal = new bootstrap.Modal(document.getElementById('productModal'));

            // Event untuk Tambah Produk
            document.getElementById('createProductButton').addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = "Tambah Produk";
                document.getElementById('productForm').setAttribute('action',
                    "{{ route('produk.store') }}");
                document.getElementById('method').value = "POST";
                document.getElementById('submitButton').innerText = "Simpan";

                // Reset Form Input
                document.getElementById('kode_barang').value = "";
                document.getElementById('nama_barang').value = "";
                document.getElementById('kategori_id').value = "";
                document.getElementById('harga_beli').value = "";
                document.getElementById('harga_jual').value = "";
                document.getElementById('satuan').value = "";

                productModal.show();
            });

            // Event untuk Edit Produk
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let kode = this.getAttribute('data-kode');
                    let nama = this.getAttribute('data-nama');
                    let kategori = this.getAttribute('data-kategori');
                    let beli = this.getAttribute('data-beli');
                    let jual = this.getAttribute('data-jual');
                    let satuan = this.getAttribute('data-satuan');

                    document.getElementById('modalTitle').innerText = "Edit Produk";
                    document.getElementById('productForm').setAttribute('action',
                        `{{ url('produk') }}/${id}`);
                    document.getElementById('method').value = "PUT";
                    document.getElementById('submitButton').innerText = "Update";

                    document.getElementById('kode_barang').value = kode;
                    document.getElementById('nama_barang').value = nama;
                    document.getElementById('kategori_id').value = kategori;
                    document.getElementById('harga_beli').value = beli;
                    document.getElementById('harga_jual').value = jual;
                    document.getElementById('satuan').value = satuan;

                    productModal.show();
                });
            });

            // Event untuk Hapus Produk
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let nama = this.getAttribute('data-nama');

                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Produk "${nama}" akan dihapus secara permanen!`,
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

@endsection
