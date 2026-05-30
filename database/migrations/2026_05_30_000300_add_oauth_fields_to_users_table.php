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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('role');
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('avatar_url')->nullable()->after('provider_id');
            $table->timestamp('last_login_at')->nullable()->after('avatar_url');
            $table->foreignId('created_by')->nullable()->after('last_login_at')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->index(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['provider', 'provider_id']);
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['provider', 'provider_id', 'avatar_url', 'last_login_at']);
        });
    }
};
