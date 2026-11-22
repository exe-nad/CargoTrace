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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('weight', 10, 2)->default(0.00); // kg
            $table->decimal('volume', 10, 2)->default(0.00); // cbm
            $table->string('package_type')->default('Standard Box'); // Box, Pallet, Drum, Crate, Container
            $table->string('hs_code', 20)->nullable(); // e.g. 8471.30
            $table->timestamps();

            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
