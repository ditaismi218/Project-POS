<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPenjualanExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $laporan;

    public function __construct(Collection $laporan)
    {
        $this->laporan = $laporan;
    }

    public function collection()
    {
        return $this->laporan->map(function ($item) {
            return [
                $item->kode_barang,
                $item->nama_barang,
                $item->nama_kategori,
                $item->total_terjual,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Total Terjual',
        ];
    }
}
