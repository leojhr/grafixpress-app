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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('quantity')->default(0);
            $table->decimal('supply_cost', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->boolean('is_service')->default(0);
            $table->timestamp('supply_date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
