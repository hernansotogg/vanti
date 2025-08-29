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
         if (!Schema::hasTable('clientes')) {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 50)->default('');
            $table->uuid('uuid');
            $table->string('empresa', 50)->default('');
             $table->string('referencia', 50)->default('');
            $table->string('status', 50)->default('');
            $table->string('dispositivo', 900)->default('');

            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
