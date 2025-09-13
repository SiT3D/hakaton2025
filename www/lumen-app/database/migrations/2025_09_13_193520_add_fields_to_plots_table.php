<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->string('livestock')->nullable();
            $table->text('livestock_description')->nullable();
            $table->integer('livestock_count')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn([
                'livestock',
                'livestock_description',
                'livestock_count',
            ]);
        });
    }
};
