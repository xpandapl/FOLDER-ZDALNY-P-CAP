<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetencyTeamValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competency_team_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competency_id');
            $table->unsignedBigInteger('team_id');
            $table->integer('value')->default(0);
            $table->timestamps();
    
            $table->foreign('competency_id')->references('id')->on('competencies')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('competency_team_values');
    }
    
}
