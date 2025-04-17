<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements OnEachRow, WithHeadingRow
{
    public $inserted = 0;
    public $skipped = 0;
    public $duplicates = [];

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $exists = Supplier::where('nama_supplier', $row['nama_supplier'])
            ->where('telepon', $row['telepon'])
            ->exists();

        if (!$exists) {
            Supplier::create([
                'nama_supplier' => $row['nama_supplier'],
                'telepon'       => $row['telepon'],
                'email'         => $row['email'],
                'alamat'        => $row['alamat'],
            ]);
            $this->inserted++;
        } else {
            $this->skipped++;
            $this->duplicates[] = $row['nama_supplier'] . ' (' . $row['telepon'] . ')';
        }
    }
}
