<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan');
            $table->text('deskripsi');
            $table->integer('kapasitas_min');
            $table->integer('kapasitas_max');
            $table->text('fasilitas'); // JSON atau text
            $table->boolean('tersedia')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ruangan');
    }
};