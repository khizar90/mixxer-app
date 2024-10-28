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
            $table->string('relationship')->after('age')->default('');
            $table->string('zodiac_sign')->after('language')->default('');
            $table->string('season')->after('zodiac_sign')->default('');
            $table->string('hometown')->after('season')->default('');
            $table->string('dietary_preferences')->after('hometown')->default('');
            $table->string('health_goals')->after('dietary_preferences')->default('');
            $table->string('weekend_must_do')->after('health_goals')->default('');
            $table->string('pet_count')->after('weekend_must_do')->default('');
            $table->string('pet_type')->after('pet_count')->default('');
            $table->string('unwind')->after('pet_type')->default('');
            $table->string('music_genres')->after('pet_type')->default('');
            $table->string('movie_genres')->after('music_genres')->default('');
            $table->string('go_to_drinks')->after('movie_genres')->default('');
            $table->string('sports_genres')->after('go_to_drinks')->default('');
            $table->string('eat_one_food')->after('sports_genres')->default('');
            $table->string('education_type')->after('eat_one_food')->default('');
            $table->string('degree')->after('education_type')->default('');
            $table->string('school_name')->after('degree')->default('');
            $table->string('work_from_anywhere')->after('school_name')->default('');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('relationship');
            $table->dropColumn('zodiac_sign');
            $table->dropColumn('season');
            $table->dropColumn('hometown');
            $table->dropColumn('dietary_preferences');
            $table->dropColumn('health_goals');
            $table->dropColumn('weekend_must_do');
            $table->dropColumn('pet_count');
            $table->dropColumn('pet_type');
            $table->dropColumn('unwind');
            $table->dropColumn('music_genres');
            $table->dropColumn('movie_genres');
            $table->dropColumn('go_to_drinks');
            $table->dropColumn('sports_genres');
            $table->dropColumn('eat_one_food');
            $table->dropColumn('education_type');
            $table->dropColumn('degree');
            $table->dropColumn('school_name');
            $table->dropColumn('work_from_anywhere');
        });
    }
};
