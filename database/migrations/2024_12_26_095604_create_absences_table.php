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
        Schema::create('absence_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->boolean('employee_creation')->default(true);
            $table->timestamps();
        });

        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();

            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->datetime('estimated_end_date')->nullable();

            $table->boolean('is_medically_certified')->default(false);
            $table->boolean('occupational')->default(false);

            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();

            $table->foreignId('absence_type_id')->constrained('absence_types')->cascadeOnDelete();

            $table->boolean('is_paid')->default(true);

            $table->text('notes')->nullable(); // reason or details

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
        Schema::dropIfExists('absence_types');
    }
};
