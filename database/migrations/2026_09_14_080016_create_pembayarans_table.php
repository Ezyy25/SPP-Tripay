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
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tagihan_id')->constrained('tagihans')->onDelete('cascade');
        $table->string('kode_transaksi')->nullable();
        $table->foreignId('siswa_id')->nullable(); // <-- Kolom yang hilang
        $table->decimal('nominal', 12, 2)->nullable();
        $table->string('bulan')->nullable();
        $table->string('tahun')->nullable();
        $table->string('metode_pembayaran')->nullable();
        $table->string('reference')->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
