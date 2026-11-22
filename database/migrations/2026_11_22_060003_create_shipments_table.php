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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // e.g. SHP-2024-001284
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('carriers')->restrictOnDelete();
            $table->foreignId('origin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('destination_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('current_location_id')->nullable()->constrained('locations')->nullOnDelete();

            $table->string('cargo_type')->default('General Cargo'); // Electronics, Textiles, Perishables, Machinery, Chemicals
            $table->decimal('total_weight', 10, 2)->default(0.00); // in kg
            $table->decimal('total_volume', 10, 2)->default(0.00); // in cbm

            $table->dateTime('expected_departure');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('expected_arrival');
            $table->dateTime('estimated_arrival');
            $table->dateTime('actual_arrival')->nullable();

            $table->enum('current_status', ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'])->default('CREATED')->index();
            $table->boolean('is_delayed')->default(false)->index();
            $table->decimal('delay_hours', 8, 2)->default(0.00);
            $table->enum('delay_severity', ['NONE', 'LOW', 'MEDIUM', 'HIGH'])->default('NONE')->index();
            $table->string('group_scenario')->nullable()->default('Default'); // Group A (12h), Group B (18h), Group C (24h)

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'current_status']);
            $table->index(['carrier_id', 'current_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
