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
        Schema::create('mixxers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('cover')->default('');
            $table->string('title');
            $table->string('categories');
            $table->string('start_date');
            $table->boolean('is_all_day')->default(0);
            $table->string('start_time');
            $table->string('start_timestamp');
            $table->string('end_time');
            $table->string('end_timestamp');
            $table->string('type');
            $table->string('limit_audience')->default('');
            $table->string('gender')->default('');
            $table->string('age_limit')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->longText('address')->default('');
            $table->string('website_link')->default('');
            $table->string('registration_link')->default('');
            $table->longText('photos')->default('');
            $table->longText('doc')->default('');
            $table->longText('description');
            $table->string('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mixxers');
    }
};
