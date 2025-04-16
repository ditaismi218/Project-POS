<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Mengambil koleksi data absensi untuk export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $absensis = Absensi::with('user')
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        return $absensis->map(function ($absen, $index) {
            // Menampilkan 'Belum Selesai' jika waktu selesai kerja kosong
            $waktuSelesai = $absen->waktu_selesai_kerja ?? 'Belum Selesai';

            return [
                'No' => $index + 1,
                'Nama Karyawan' => $absen->user->name ?? '-',
                'Tanggal Masuk' => \Carbon\Carbon::parse($absen->tanggal_masuk)->format('d-m-Y'),
                'Waktu Masuk' => $absen->waktu_masuk,
                'Status' => ucwords($absen->status_masuk),
                'Waktu Selesai Kerja' => $waktuSelesai,
            ];
        });
    }

    /**
     * Menentukan judul kolom untuk export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Tanggal Masuk',
            'Waktu Masuk',
            'Status',
            'Waktu Selesai Kerja',
        ];
    }
}
