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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignUuid('person_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('body');
            $table->string('type');
            $table->unsignedBigInteger('data_id');
            $table->string('time');
            $table->string('date');
            $table->boolean('is_read')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
