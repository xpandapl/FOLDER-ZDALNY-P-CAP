<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCompetencyNameColumnInCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('competency_name', 500)->change(); // Zmieniamy długość na 500 znaków
        });
    }
    
    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('competency_name', 255)->change(); // Przywracamy pierwotną długość
        });
    }
    
}
