<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompetencyTypeToCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('competency_type')->nullable(); // Add competency_type column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('competency_type'); // Remove the column if rolled back
        });
    }
}
