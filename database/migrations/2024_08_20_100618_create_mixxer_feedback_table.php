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
        Schema::create('mixxer_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignId('mixxer_id')->constrained('mixxers')->onDelete('cascade');
            $table->string('experience');
            $table->text('highlights');
            $table->text('experience_encourage');
            $table->text('improvement');
            $table->text('expecting');
            $table->text('have_fun');
            $table->string('experience_socializing');
            $table->text('group_fun');
            $table->text('rate_the_venue');
            $table->text('virtual_setting');
            $table->text('additional_comment');
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mixxer_feedback');
    }
};
