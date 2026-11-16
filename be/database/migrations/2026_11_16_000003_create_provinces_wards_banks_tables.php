<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary();
            $table->string('name', 150);
            $table->string('division_type', 50)->nullable();
            $table->string('codename', 100)->nullable();
            $table->integer('phone_code')->nullable();
            $table->timestamps();
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary();
            $table->unsignedBigInteger('province_code');
            $table->string('name', 150);
            $table->string('division_type', 50)->nullable();
            $table->string('codename', 100)->nullable();
            $table->string('short_codename', 100)->nullable();
            $table->timestamps();

            $table->foreign('province_code')
                ->references('code')
                ->on('provinces')
                ->cascadeOnDelete();
            $table->index('province_code');
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name', 255);
            $table->string('code', 50)->nullable();
            $table->string('bin', 20)->nullable();
            $table->string('short_name', 100)->nullable();
            $table->string('logo', 255)->nullable();
            $table->boolean('transfer_supported')->default(false);
            $table->boolean('lookup_supported')->default(false);
            $table->integer('support')->default(0);
            $table->boolean('is_transfer')->default(false);
            $table->string('swift_code', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('provinces');
    }
};
