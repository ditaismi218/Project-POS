<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 255);
            $table->string('gambar')->nullable();

            $table->bigInteger('kategori_id')->unsigned();
            $table->enum('satuan', ['pcs', 'pack', 'box', 'lusin', 'gram', 'kg', 'ml', 'liter', 'meter', 'botol', 'kaleng', 'sachet', 'strip']);
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('kategori_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
