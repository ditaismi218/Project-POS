@extends('layouts.layout')
@section('title', 'Manajemen Produk')

@section('content')

    <div class="page-body">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <!-- Tombol Tambah Pengajuan -->
            <button class="btn btn-primary btn-sm" id="createProductButton" data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="fa fa-plus"></i> Tambah Pengajuan Barang
            </button>
            <div class="d-flex align-items-center gap-2">
                <!-- Form Filter -->
                <form method="GET" action="{{ route('pengajuan_barang.index') }}" class="d-flex align-items-center gap-1">
                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm w-auto"
                        value="{{ request('tanggal_mulai') }}">
                    <span class="mx-1">s/d</span>
                    <input type="date" name="tanggal_selesai" class="form-control form-control-sm w-auto"
                        value="{{ request('tanggal_selesai') }}">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('pengajuan_barang.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                </form>

                <div class="dropdown">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li>
                           
                            <a class="dropdown-item"
                             href="{{ route('pengajuan_barang.index', ['export_excel' => true]) }}"><i
                                class="fas fa-file-pdf"></i> Ekspor Excel</a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('pengajuan_barang.index', ['export_pdf' => true, 'tanggal_mulai' => request('tanggal_mulai'), 'tanggal_selesai' => request('tanggal_selesai')]) }}"><i
                                    class="fas fa-file-pdf"></i> Ekspor PDF</a>
                        </li>
                        <li><a class="dropdown-item" href="#" id="exportPrint">Print</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong class="font-bold">Error!</strong>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Pengajuan Barang</h5>
            <div class="card-datatable pb-6 px-6">
                <table class="table table-bordered" id="pengajuanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengaju</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Pengajuan</th>
                            <th>QTY</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($pengajuanBarang as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->nama_pengaju }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->tanggal_pengajuan }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            id="terpenuhiSwitch{{ $item->id }}"
                                            {{ $item->terpenuhi == 1 ? 'checked' : '' }}
                                            onchange="updateTerpenuhi({{ $item->id }}, this.checked)">
                                    </div>
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $item->id }}"
                                        @if ($item->terpenuhi === 1) disabled @endif>
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_barang }}')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                    <!-- Form Hapus -->
                                    <form id="deleteForm{{ $item->id }}"
                                        action="{{ route('pengajuan_barang.destroy', $item->id) }}" method="POST"
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

    <!-- Modal Edit Pengajuan Barang (Pindah ke luar tabel) -->
    @foreach ($pengajuanBarang as $item)
        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
            aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('pengajuan_barang.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengajuan Barang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                                <select class="form-select" name="nama_pengaju" required>
                                    <option value="">Pilih Nama Pengaju</option>
                                    @foreach ($namaPengajuEnum as $nama)
                                        <option value="{{ $nama }}"
                                            {{ $item->nama_pengaju === $nama ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" name="nama_barang"
                                    value="{{ $item->nama_barang }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="qty" class="form-label">QTY</label>
                                <input type="number" class="form-control" name="qty" value="{{ $item->qty }}"
                                    min="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Tambah Pengajuan Barang -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('pengajuan_barang.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Pengajuan Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                            <select class="form-select" name="nama_pengaju" required>
                                <option value="">Pilih Nama Pengaju</option>
                                @foreach ($namaPengajuEnum as $nama)
                                    <option value="{{ $nama }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="qty" class="form-label">QTY</label>
                            <input type="number" class="form-control" name="qty" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, namaBarang) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus pengajuan barang " + namaBarang,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>

    <script>
        function updateTerpenuhi(id, terpenuhi) {
            axios.post(`/pengajuan-barang/update-terpenuhi/${id}`, {
                    terpenuhi: terpenuhi ? 1 : 0
                })
                .then(response => {
                    console.log(response.data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memperbarui status terpenuhi.');
                });
        }
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#pengajuanTable').DataTable({
                dom: "<'row'<'col-md-6'l><'col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        title: 'Pengajuan-Barang',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 5) {
                                        return $(node).find('input[type="checkbox"]').is(
                                            ':checked') ? 'Terpenuhi' : 'Belum Terpenuhi';
                                    }
                                    return data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        title: 'Pengajuan-Barang',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 5) {
                                        return $(node).find('input[type="checkbox"]').is(
                                            ':checked') ? 'Terpenuhi' : 'Belum Terpenuhi';
                                    }
                                    return data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary',
                        title: 'Pengajuan-Barang',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 5) {
                                        return $(node).find('input[type="checkbox"]').is(
                                            ':checked') ? 'Terpenuhi' : 'Belum Terpenuhi';
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ],
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                pagingType: "simple_numbers",
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/Indonesian.json",
                    paginate: {
                        previous: "<", // Tombol Previous dengan simbol <
                        next: ">" // Tombol Next dengan simbol >
                    }
                }
            });

            // Event Klik Export
            $('#exportExcel').click(function() {
                table.button(0).trigger();
            });
            $('#exportPDF').click(function() {
                table.button(1).trigger();
            });
            $('#exportPrint').click(function() {
                table.button(2).trigger();
            });
        });
    </script>
@endpush
