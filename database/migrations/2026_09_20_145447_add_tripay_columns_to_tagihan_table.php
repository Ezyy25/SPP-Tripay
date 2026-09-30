<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('reference');
            $table->string('checkout_url')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn(['reference', 'payment_method', 'checkout_url']);
        });
    }
};