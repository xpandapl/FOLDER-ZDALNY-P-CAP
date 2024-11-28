<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCompetencyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('employee_competency_values', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('employee_id');
        $table->unsignedBigInteger('competency_id');
        $table->decimal('value', 8, 2);
        $table->timestamps();

        $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        $table->foreign('competency_id')->references('id')->on('competencies')->onDelete('cascade');
        $table->unique(['employee_id', 'competency_id']);
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_competency_values');
    }
}
