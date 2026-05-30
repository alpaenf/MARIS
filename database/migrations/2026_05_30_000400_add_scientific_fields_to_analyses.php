<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->double('hazard_score')->nullable();
            $table->double('exposure_score')->nullable();
            $table->double('vulnerability_score')->nullable();
            $table->string('dominant_species')->nullable();
            $table->string('soil_type')->default('mineral'); // mineral / organik
            $table->double('carbon_agb')->nullable();
            $table->double('carbon_bgb')->nullable();
            $table->double('carbon_soc')->nullable();
            $table->json('dpsir_payload')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn([
                'hazard_score', 'exposure_score', 'vulnerability_score',
                'dominant_species', 'soil_type',
                'carbon_agb', 'carbon_bgb', 'carbon_soc',
                'dpsir_payload'
            ]);
        });
    }
};
