<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('suppliers', 'tax_code')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('tax_code');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('suppliers', 'tax_code')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('tax_code', 30)->nullable()->after('email');
            });
        }
    }
};
