<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->double('mcvi')->nullable()->after('vulnerability_score');
            $table->double('mrps')->nullable()->after('mcvi');
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn(['mcvi', 'mrps']);
        });
    }
};
