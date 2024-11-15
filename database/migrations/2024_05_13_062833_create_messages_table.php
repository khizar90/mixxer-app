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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->unsignedBigInteger('ticket_id')->default(0);
            $table->unsignedBigInteger('mixxer_id')->default(0);
            $table->string('from_to')->default('');
            $table->text('type');
            $table->longText('message');
            $table->string('attachment')->default('');
            $table->boolean('is_read')->default(0);
            $table->string('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
