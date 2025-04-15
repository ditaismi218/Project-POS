<?php

namespace App\Imports;

use App\Models\KategoriProduk;
use Maatwebsite\Excel\Concerns\ToModel;

class KategoriImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new KategoriProduk([
            'nama_kategori' => $row[0], 
        ]);
    }
}
