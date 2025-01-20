<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favourites', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->char('token_name', length: 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
