<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->tinyInteger('period')->nullable(); // null = yearly, 1/2 = half-year, flexible
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->unique(['year','period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_cycles');
    }
};
