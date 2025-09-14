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
        Schema::create('plot_summaries', function (Blueprint $table) {
            $table->id();
            $table->text('summary');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slice_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_summaries');
    }
};
