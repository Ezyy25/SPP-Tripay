<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihans', 'description')) {
                $table->text('description')->nullable()->after('nominal');
            }

            if (!Schema::hasColumn('tagihans', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            if (Schema::hasColumn('tagihans', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('tagihans', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};