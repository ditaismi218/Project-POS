<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher')->unique();
            $table->enum('jenis', ['persentase', 'nominal']); 
            $table->double('nilai'); 
            $table->double('min_belanja'); 
            $table->date('berlaku_hingga'); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};
