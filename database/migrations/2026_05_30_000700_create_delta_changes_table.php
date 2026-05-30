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
        Schema::create('delta_changes', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('region_name');
            $table->timestamp('snapshot_at');
            $table->string('change_type');
            $table->json('changed_fields')->nullable();
            $table->foreignId('from_ingest_id')->nullable()->constrained('raw_ingests')->nullOnDelete();
            $table->foreignId('to_ingest_id')->nullable()->constrained('raw_ingests')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
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
        Schema::dropIfExists('delta_changes');
    }
};
