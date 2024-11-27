<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLevelColumnInCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('level', 1000)->change();  // Zwiększ rozmiar kolumny `level`
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
            $table->string('level', 255)->change();  // Przywróć poprzedni rozmiar kolumny
        });
    }
}
