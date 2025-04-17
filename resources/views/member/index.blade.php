@extends('layouts.layout')
@section('title', 'Penerimaan Barang')

@section('content')
    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Member
            </button>
            <!-- Tombol untuk memunculkan modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            Import Excel
        </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Member</h5>
            <div class="card-datatable">
                <table class="table table-striped dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Member</th>
                            <th>Nama</th>
                            <th>No Telepon</th>
                            <th>Alamat</th>
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
                                <td>{{ $item->tgl_bergabung }}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_member }}" data-nama="{{ $item->nama }}"
                                        data-telp="{{ $item->no_telp }}" data-alamat="{{ $item->alamat }}"
                                        data-tgl="{{ $item->tgl_bergabung }}" data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('member.destroy', $item->id) }}" method="POST"
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

    {{-- Modal Tambah --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" action="{{ route('member.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No Telp</label>
                            <input type="number" class="form-control" id="no_telp" name="no_telp" maxlength="12"
                                oninput="this.value = this.value.slice(0, 12)" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
                        </div>



                        <div class="mb-3">
                            <label for="tgl_bergabung" class="form-label">Tanggal Bergabung</label>
                            <input type="date" class="form-control" id="tgl_bergabung" name="tgl_bergabung" required>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-3">
                            <label for="kode_member-edit" class="form-label">Kode Member</label>
                            <input type="text" class="form-control" id="kode_member-edit" name="kode_member"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label for="nama-edit" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama-edit" name="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_telp-edit" class="form-label">No Telp</label>
                            <input type="number" class="form-control" id="no_telp-edit" name="no_telp" maxlength="12"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat-edit" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat-edit" name="alamat" rows="2" required></textarea>
                        </div>



                        <div class="mb-3">
                            <label for="tgl_bergabung-edit" class="form-label">Tanggal Bergabung</label>
                            <input type="date" class="form-control" id="tgl_bergabung-edit" name="tgl_bergabung"
                                required>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="file" class="form-label">Pilih File Excel</label>
              <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls, .csv">
            </div>
            <button type="submit" class="btn btn-success">Import</button>
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
            // Inisialisasi DataTable
            const table = $('.table').DataTable({
                scrollX: true,
                responsive: true
            });

            // Inisialisasi modal
            const productModal = new bootstrap.Modal('#productModal');
            const editModal = new bootstrap.Modal('#editModal');

            // Event untuk Tambah Member
            $('#createProductButton').click(function() {
                $('#productForm')[0].reset();
                productModal.show();
            });

            // Event untuk Edit Member
            $(document).on('click', '.edit-button', function() {
                const id = $(this).data('id');
                const kode = $(this).data('kode');
                const nama = $(this).data('nama');
                const telp = $(this).data('telp');
                const alamat = $(this).data('alamat');
                const points = $(this).data('points');
                const tgl = $(this).data('tgl');

                console.log('Editing member:', {
                    id,
                    kode,
                    nama,
                    telp,
                    alamat,
                    points,
                    tgl
                });

                $('#edit_id').val(id);
                $('#kode_member-edit').val(kode);
                $('#nama-edit').val(nama);
                $('#no_telp-edit').val(telp);
                $('#alamat-edit').val(alamat);
                $('#tgl_bergabung-edit').val(tgl);
                $('#editForm').attr('action', '/member/' + id);
            });

            // Event untuk Delete Member
            $(document).on('click', '.delete-button', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: `Hapus member ${nama}?`,
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush
