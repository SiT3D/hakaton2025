<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cadastral_number')->unique();
            $table->date('sowing_date')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('land_use');
            $table->string('culture')->nullable();
            $table->text('culture_description')->nullable();

            $table->geometry('geometry', 4326)->nullable();
            $table->spatialIndex('geometry');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
