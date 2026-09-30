<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayarans', 'kode_transaksi')) {
                $table->string('kode_transaksi')->nullable()->after('tagihan_id');
            }
            if (!Schema::hasColumn('pembayarans', 'siswa_id')) {
                $table->foreignId('siswa_id')->nullable()->after('kode_transaksi');
            }
            if (!Schema::hasColumn('pembayarans', 'nominal')) {
                $table->decimal('nominal', 12, 2)->nullable()->after('siswa_id');
            }
            if (!Schema::hasColumn('pembayarans', 'bulan')) {
                $table->string('bulan')->nullable()->after('nominal');
            }
            if (!Schema::hasColumn('pembayarans', 'tahun')) {
                $table->string('tahun')->nullable()->after('bulan');
            }
            if (!Schema::hasColumn('pembayarans', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable()->after('tahun');
            }
            if (!Schema::hasColumn('pembayarans', 'reference')) {
                $table->string('reference')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('pembayarans', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            //
        });
    }
};