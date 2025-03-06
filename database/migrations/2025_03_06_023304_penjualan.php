<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->dateTime('tgl_faktur');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained('member')->onDelete('set null');
            $table->double('total_bayar');
            $table->enum('status', ['lunas', 'belum_lunas', 'batal'])->default('belum_lunas');
            $table->foreignId('voucher_id')->nullable()->constrained('voucher')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
};
