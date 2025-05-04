<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('komunitas', function (Blueprint $table) {
        if (!Schema::hasColumn('komunitas', 'password')) {
            $table->string('password');
        }
        if (!Schema::hasColumn('komunitas', 'remember_token')) {
            $table->rememberToken();
        }
        if (!Schema::hasColumn('komunitas', 'created_at')) {
            $table->timestamps();
        }
    });
}

    public function down()
    {
        Schema::table('komunitas', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'created_at', 'updated_at']);
        });
    }
};