<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_logs', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country', 10)->nullable();
            $table->decimal('temperature', 5, 2);
            $table->decimal('feels_like', 5, 2)->nullable();
            $table->integer('humidity')->nullable();
            $table->decimal('wind_speed', 6, 2)->nullable();
            $table->integer('wind_deg')->nullable();
            $table->integer('pressure')->nullable();
            $table->integer('visibility')->nullable();
            $table->integer('clouds')->nullable();
            $table->string('description')->nullable();
            $table->string('icon', 10)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();
            $table->string('source', 50)->default('openweathermap');
            $table->json('raw_response')->nullable();
            $table->timestamp('observed_at')->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_logs');
    }
};
