<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MemberImport implements ToModel, WithHeadingRow, WithValidation
{
    public array $skippedRows = [];
    public int $importedCount = 0;

    public function model(array $row)
    {
        $nama = trim($row['nama']);
        $no_telp = trim($row['no_telp']);

        // Cek duplikat
        $exists = Member::where('nama', $nama)
            ->where('no_telp', $no_telp)
            ->exists();

        if ($exists) {
            $this->skippedRows[] = $row;
            return null;
        }

        $this->importedCount++; // ✅ Hitung data yang berhasil diimport

        return new Member([
            'kode_member' => 'MBR-' . uniqid(),
            'nama' => $nama,
            'no_telp' => $no_telp,
            'alamat' => trim($row['alamat']),
            'tgl_bergabung' => \Carbon\Carbon::parse($row['tgl_bergabung']),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'no_telp' => 'required|numeric|digits_between:10,12',
            'alamat' => 'required|string',
            'tgl_bergabung' => 'required|date',
        ];
    }
}
