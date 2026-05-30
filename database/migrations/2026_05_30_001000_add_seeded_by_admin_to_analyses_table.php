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
        Schema::table('analyses', function (Blueprint $table) {
            $table->boolean('seeded_by_admin')->default(false)->after('user_id');
            $table->index('seeded_by_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropIndex(['seeded_by_admin']);
            $table->dropColumn('seeded_by_admin');
        });
    }
};
