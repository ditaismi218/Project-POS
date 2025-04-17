@extends('layouts.layout')
@section('title', 'Absensi Kerja')

@section('content')
    <div class="page-body">
        <div class="mb-4 d-flex justify-content-start gap-2 align-items-center">
            <!-- Kiri: Tombol Tambah -->
            <button class="btn btn-primary" id="createAbsensiButton">
                <i class="fa fa-plus"></i> Tambah Absensi
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownExportButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download"></i> Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownExportButton">
                    <li>
                        <a class="dropdown-item" href="{{ route('absensi.index', ['export_pdf' => 1]) }}">
                            <i class="fa fa-file-pdf text-danger me-2"></i> Export PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('absensi.export') }}">
                            <i class="fa fa-file-excel text-success me-2"></i> Export Excel
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tombol Import -->
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa fa-file-import"></i> Import Excel
            </button>

            <!-- Kanan: Export dan Import -->
            {{-- <div class="d-flex gap-2">
                <!-- Dropdown Export -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownExportButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-download"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownExportButton">
                        <li>
                            <a class="dropdown-item" href="{{ route('absensi.index', ['export_pdf' => 1]) }}">
                                <i class="fa fa-file-pdf text-danger me-2"></i> Export PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('absensi.export') }}">
                                <i class="fa fa-file-excel text-success me-2"></i> Export Excel
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tombol Import -->
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fa fa-file-import"></i> Import Excel
                </button>
            </div> --}}
        </div>


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif



        <div class="card">
            <h5 class="card-header">Tabel Absensi Kerja</h5>
            <div class="card-datatable">
                <table class="dt-scrollableTable table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Tanggal Masuk</th>
                            <th>Waktu Masuk</th>
                            <th>Status</th>
                            <th>Waktu Selesai Kerja</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensi as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->tanggal_masuk }}</td>
                                <td>{{ $item->waktu_masuk }}</td>
                                <td>
                                    <select class="form-control status-select" data-id="{{ $item->id }}">
                                        <option value="masuk" {{ $item->status_masuk == 'masuk' ? 'selected' : '' }}>Masuk
                                        </option>
                                        <option value="sakit" {{ $item->status_masuk == 'sakit' ? 'selected' : '' }}>Sakit
                                        </option>
                                        <option value="cuti" {{ $item->status_masuk == 'cuti' ? 'selected' : '' }}>Cuti
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    @if ($item->status_masuk == 'masuk' && !$item->waktu_selesai_kerja)
                                        <button class="btn btn-success btn-sm selesai-button"
                                            data-id="{{ $item->id }}">
                                            Selesai
                                        </button>
                                    @else
                                        {{ $item->waktu_selesai_kerja ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-button" data-id="{{ $item->id }}"
                                        data-user="{{ $item->user_id }}" data-tanggal="{{ $item->tanggal_masuk }}"
                                        data-waktu="{{ $item->waktu_masuk }}" data-status="{{ $item->status_masuk }}"
                                        data-bs-toggle="modal" data-bs-target="#editAbsensiModal">
                                        Edit
                                    </button>

                                    <form action="{{ route('absensi.destroy', $item->id) }}" method="POST"
                                        class="form-delete d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm delete-button"
                                            data-id="{{ $item->id }}">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Tambah Absensi  --}}
        <div class="modal fade" id="absensiModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('absensi.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Karyawan</label>
                                <select name="user_id" class="form-control" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status_masuk" class="form-control">
                                    <option value="masuk">Masuk</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="cuti">Cuti</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Waktu Masuk</label>
                                <input type="time" name="waktu_masuk" class="form-control" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Edit Absensi --}}
        <div class="modal fade" id="editAbsensiModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="editAbsensiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Absensi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Karyawan</label>
                                <select name="user_id" class="form-control" id="editUser" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" id="editTanggal" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Waktu Masuk</label>
                                <input type="time" name="waktu_masuk" id="editWaktu" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status_masuk" id="editStatus" class="form-control">
                                    <option value="masuk">Masuk</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="cuti">Cuti</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Import Excel -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Absensi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Excel</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                                <small class="text-muted">Hanya file dengan format .xls atau .xlsx</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.dt-scrollableTable').DataTable();

            $('#createAbsensiButton').on('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('absensiModal'));
                modal.show();
            });

            // Delegasi untuk tombol hapus
            $(document).on('click', '.delete-button', function() {
                let id = $(this).data('id');
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Delegasi untuk tombol edit
            $(document).on('click', '.edit-button', function() {
                let id = $(this).data('id');
                let user = $(this).data('user');
                let tanggal = $(this).data('tanggal');
                let waktu = $(this).data('waktu');
                let status = $(this).data('status');

                $('#editUser').val(user);
                $('#editTanggal').val(tanggal);
                $('#editWaktu').val(waktu);
                $('#editStatus').val(status);
                $('#editAbsensiForm').attr('action', '/absensi/' + id);
            });

            // Delegasi untuk select status
            $(document).on('change', '.status-select', function() {
                let status = $(this).val();
                let id = $(this).data('id');

                $.post(`{{ route('absensi.updateStatus') }}`, {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                }, function(response) {
                    location.reload();
                });
            });

            // Delegasi untuk tombol selesai
            $(document).on('click', '.selesai-button', function() {
                let id = $(this).data('id');

                $.post(`{{ route('absensi.selesaikan') }}`, {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(response) {
                    location.reload();
                });
            });
        });
    </script>
@endpush
