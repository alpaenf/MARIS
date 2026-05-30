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
        Schema::create('raw_ingests', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->timestamp('fetched_at');
            $table->string('checksum', 64)->nullable();
            $table->json('payload');
            $table->string('status')->default('ok');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['source', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_ingests');
    }
};
