<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('suppliers', 'district_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('district_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('suppliers', 'district_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            });
        }
    }
};
