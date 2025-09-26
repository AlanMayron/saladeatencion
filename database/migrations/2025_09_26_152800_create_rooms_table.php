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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('name', 120)->unique();
        $table->unsignedInteger('capacity'); // >= 1 (se valida en servidor)
        $table->string('status', 20); // 'disponible', 'ocupada', 'mantenimiento' (validamos en servidor)
        $table->timestamps();
        // Extras opcionales después: $table->softDeletes(); $table->string('location')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
