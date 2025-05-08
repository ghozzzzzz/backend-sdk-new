<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_komunitas')->constrained('komunitas', 'id_komunitas');
            $table->foreignId('ruangan_id')->constrained('ruangan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->integer('jumlah_peserta');
            $table->text('kebutuhan_khusus')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemesanan');
    }
};