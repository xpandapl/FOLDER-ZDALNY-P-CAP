<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedBigInteger('cycle_id')->nullable()->after('employee_id');
            $table->unsignedBigInteger('parent_result_id')->nullable()->after('cycle_id');
            $table->foreign('cycle_id')->references('id')->on('assessment_cycles')->nullOnDelete();
            $table->foreign('parent_result_id')->references('id')->on('results')->nullOnDelete();
            $table->index(['employee_id','cycle_id']);
            $table->index(['competency_id','cycle_id']);
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['cycle_id']);
            $table->dropForeign(['parent_result_id']);
            $table->dropIndex(['employee_id','cycle_id']);
            $table->dropIndex(['competency_id','cycle_id']);
            $table->dropColumn(['cycle_id','parent_result_id']);
        });
    }
};
