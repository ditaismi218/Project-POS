<?php

namespace App\Observers;

use App\Models\PenerimaanBarang;
use App\Models\PengajuanBarang;

class PenerimaanBarangObserver
{
    /**
     * Handle the PenerimaanBarang "created" event.
     */
    public function created(PenerimaanBarang $penerimaanBarang)
    {
        // Ambil nama barang dari relasi produk
        $namaBarang = $penerimaanBarang->produk->nama_barang;

        // Update status terpenuhi di pengajuan barang jika ada nama barang yang sama
        PengajuanBarang::where('nama_barang', $namaBarang)
            ->update(['terpenuhi' => 1]);
    }

    /**
     * Handle the PenerimaanBarang "updated" event.
     */
    public function updated(PenerimaanBarang $penerimaanBarang): void
    {
        //
    }

    /**
     * Handle the PenerimaanBarang "deleted" event.
     */
    public function deleted(PenerimaanBarang $penerimaanBarang): void
    {
        //
    }

    /**
     * Handle the PenerimaanBarang "restored" event.
     */
    public function restored(PenerimaanBarang $penerimaanBarang): void
    {
        //
    }

    /**
     * Handle the PenerimaanBarang "force deleted" event.
     */
    public function forceDeleted(PenerimaanBarang $penerimaanBarang): void
    {
        //
    }
}
