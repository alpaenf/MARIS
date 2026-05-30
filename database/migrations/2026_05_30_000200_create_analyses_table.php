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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('region_name');
            $table->string('province');
            $table->decimal('area_size', 12, 2);
            $table->unsignedInteger('rainfall');
            $table->unsignedInteger('abrasion_level');
            $table->unsignedInteger('flood_risk');
            $table->unsignedInteger('mangrove_loss');
            $table->unsignedInteger('population_density');
            $table->unsignedTinyInteger('climate_risk_score')->nullable();
            $table->string('restoration_priority')->nullable();
            $table->decimal('carbon_potential', 12, 2)->nullable();
            $table->text('ai_explanation')->nullable();
            $table->text('restoration_recommendation')->nullable();
            $table->json('result_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
