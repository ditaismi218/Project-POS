<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->decimal('harga_jual', 10, 2)->after('qty'); // Tambahkan kolom harga_jual setelah qty
        });
    }

    public function down()
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }

};
