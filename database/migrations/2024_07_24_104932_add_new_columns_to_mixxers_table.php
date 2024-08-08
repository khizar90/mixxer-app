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
            $table->longText('host_url');
            $table->longText('viewer_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mixxers', function (Blueprint $table) {
            $table->dropColumn('host_url');
            $table->dropColumn('viewer_url');
        });
    }
};
