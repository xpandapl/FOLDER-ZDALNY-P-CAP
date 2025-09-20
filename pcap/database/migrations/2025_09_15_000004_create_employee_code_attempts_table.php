<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_code_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('cycle_id');
            $table->string('ip_hash', 64);
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('cycle_id')->references('id')->on('assessment_cycles')->cascadeOnDelete();
            $table->unique(['cycle_id','ip_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_code_attempts');
    }
};
