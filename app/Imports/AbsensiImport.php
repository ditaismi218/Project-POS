<?php

namespace App\Imports;

use App\Models\Absensi;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AbsensiImport implements ToModel, WithHeadingRow, WithValidation
{
    public $importedCount = 0;

    public function model(array $row)
    {
        logger()->info('Importing row:', $row);

        $user = User::where('name', $row['nama'] ?? '')->first();

        if (!$user) {
            throw new \Exception("User tidak ditemukan: " . ($row['nama'] ?? 'N/A'));
        }

        $tanggalMasuk = $row['tanggal_masuk'] ?? $row['tanggal'] ?? now()->toDateString();
        $statusMasuk = $row['status_masuk'] ?? 'masuk';

        // Cek apakah absensi sudah ada
        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal_masuk', $tanggalMasuk)
            ->whereIn('status_masuk', ['masuk', 'sakit', 'cuti'])
            ->exists();

        if ($sudahAbsen) {
            logger()->info("User {$user->name} sudah absen di tanggal $tanggalMasuk, dilewati.");
            return null;
        }

        // Set waktu masuk dan waktu selesai
        $waktuMasuk = $row['waktu_masuk'] ?? '00:00:00';
        $waktuSelesai = $row['waktu_selesai_kerja'] ?? null;

        // Jika status adalah cuti atau sakit, set waktu menjadi 00:00:00
        if (in_array($statusMasuk, ['cuti', 'sakit'])) {
            $waktuMasuk = '00:00:00';
            $waktuSelesai = '00:00:00';
        }

        $this->importedCount++;

        return new Absensi([
            'user_id' => $user->id,
            'tanggal_masuk' => $tanggalMasuk,
            'waktu_masuk' => $waktuMasuk,
            'status_masuk' => $statusMasuk,
            'waktu_selesai_kerja' => $waktuSelesai,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'status_masuk' => 'required|in:masuk,sakit,cuti',
        ];
    }
}
