<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_cycle_access_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('cycle_id');
            $table->string('code_hash');
            $table->string('raw_last4', 10)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('cycle_id')->references('id')->on('assessment_cycles')->cascadeOnDelete();
            $table->unique(['employee_id','cycle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_cycle_access_codes');
    }
};
