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
        Schema::create('app_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('experience');
            $table->text('like_most');
            $table->text('confusing');
            $table->text('update');
            $table->text('content');
            $table->text('use_app');
            $table->text('notification');
            $table->text('bug');
            $table->text('support');
            $table->text('additional_support');
            $table->text('final');
            $table->string('interested');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_feedback');
    }
};
