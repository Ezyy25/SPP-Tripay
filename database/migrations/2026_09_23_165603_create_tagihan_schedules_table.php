<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('SPP Bulanan'); // Judul tagihan
            $table->integer('generate_day')->default(1); // Tanggal terbit tiap bulan (contoh: 1)
            $table->integer('due_day')->default(10); // Tanggal jatuh tempo bulan itu (contoh: 10)
            $table->decimal('nominal', 12, 2); // Jumlah nominal tagihan
            $table->boolean('is_active')->default(true); // Status jadwal aktif/tidak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_schedules');
    }
};