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
        Schema::table('raw_ingests', function (Blueprint $table) {
            $table->string('region_code')->nullable()->after('source');
            $table->string('region_name')->nullable()->after('region_code');
            $table->index(['source', 'region_code', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_ingests', function (Blueprint $table) {
            $table->dropIndex(['source', 'region_code', 'fetched_at']);
            $table->dropColumn(['region_code', 'region_name']);
        });
    }
};
