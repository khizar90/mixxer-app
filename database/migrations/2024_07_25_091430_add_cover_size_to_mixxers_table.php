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
        Schema::table('mixxers', function (Blueprint $table) {
            $table->string('cover_size')->default(0.8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mixxers', function (Blueprint $table) {
            $table->dropColumn('cover_size');
        });
    }
};