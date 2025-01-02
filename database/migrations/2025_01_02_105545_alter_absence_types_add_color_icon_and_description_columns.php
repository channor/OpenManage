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
        Schema::table('absence_types', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('color')->nullable()->after('description');
            $table->string('icon')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_types', function (Blueprint $table) {
            $table->dropColumn(['description', 'color', 'icon']);
        });
    }
};
