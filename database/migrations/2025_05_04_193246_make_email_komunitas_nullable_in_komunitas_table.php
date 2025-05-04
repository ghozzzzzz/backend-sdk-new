<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('komunitas', function (Blueprint $table) {
            $table->string('email_komunitas')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('komunitas', function (Blueprint $table) {
            $table->string('email_komunitas')->nullable(false)->change();
        });
    }
};