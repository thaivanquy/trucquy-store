<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('name', 200);
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('tax_code', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
