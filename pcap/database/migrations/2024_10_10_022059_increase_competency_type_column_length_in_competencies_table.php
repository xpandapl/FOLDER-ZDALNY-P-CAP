<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseCompetencyTypeColumnLengthInCompetenciesTable extends Migration
{
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('competency_type')->change();  // Change to TEXT to allow longer data
        });
    }

    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('competency_type', 255)->change();  // Revert back to VARCHAR(255) if needed
        });
    }
}

