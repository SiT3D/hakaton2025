<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('plots', function (Blueprint $table) {
            $table->bigInteger('owner_id')->nullable()->after('id');
             $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });
    }
};
