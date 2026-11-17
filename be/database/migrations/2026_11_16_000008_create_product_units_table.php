<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->string('barcode', 50)->nullable()->index();
            $table->decimal('conversion_rate', 12, 4)->default(1);
            $table->boolean('is_base_unit')->default(false);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->boolean('is_direct_sale')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_base_unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
