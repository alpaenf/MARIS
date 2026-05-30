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
        Schema::create('hourly_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('region_name');
            $table->timestamp('snapshot_at');
            $table->unsignedTinyInteger('hazard_score')->nullable();
            $table->unsignedTinyInteger('mcvi_score')->nullable();
            $table->unsignedTinyInteger('mrps_score')->nullable();
            $table->decimal('carbon_tons', 12, 2)->nullable();
            $table->json('metrics')->nullable();
            $table->timestamps();

            $table->index(['source', 'snapshot_at']);
            $table->index(['region_name', 'snapshot_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourly_snapshots');
    }
};
