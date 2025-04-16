<?php

namespace App\Imports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class AbsensiImport implements ToModel, WithHeadingRow
{
    use Importable;

    /**
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Absensi([
            'user_id' => $row['user_id'],
            'tanggal_masuk' => $row['tanggal_masuk'],
            'waktu_masuk' => $row['waktu_masuk'],
            'status_masuk' => $row['status_masuk'],
            'waktu_selesai_kerja' => $row['waktu_selesai_kerja'] ?? null,
        ]);
    }
}