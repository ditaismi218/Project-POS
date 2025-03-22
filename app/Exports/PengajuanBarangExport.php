<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengajuanBarangExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'ID' => $item->id,
                'Nama Pengaju' => $item->nama_pengaju,
                'Tanggal Pengajuan' => $item->tanggal_pengajuan,
                'Nama Barang' => $item->nama_barang,
                'QTY' => $item->qty,
                'Status' => $item->terpenuhi == 1 ? 'Sudah Terpenuhi' : 'Belum Terpenuhi',
            ];
        });
    }
    public function headings(): array
    {
        return ['ID', 'Nama Pengaju', 'Tanggal Pengajuan', 'Nama Barang', 'QTY', 'Status'];
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris data + header
        $totalRows = $this->data->count() + 1;

        return [
            // Styling header (baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'F4B084'],
                ],
            ],
            // Styling seluruh isi data (baris 2 hingga akhir)
            "A2:F{$totalRows}" => [
                'alignment' => [
                    'horizontal' => 'center', // Rata tengah
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,             // ID
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,      // Tanggal Pengajuan
            'E' => NumberFormat::FORMAT_NUMBER,             // QTY
        ];
    }
}
