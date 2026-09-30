<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nis')->unique();
            $table->string('nama')->nullable();
            $table->string('kelas')->nullable();
            $table->string('no_hp')->nullable();
            $table->foreignId('spp_rate_id')->nullable();
            $table->softDeletes(); // Menambahkan kolom deleted_at di sini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};