<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullInCategoryColumnInCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            // Zmieniamy typ kolumny na TEXT, aby pomieściła więcej znaków
            $table->text('category')->nullable()->change();
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
            // Przywróć wcześniejszą definicję (VARCHAR 255)
            $table->string('category', 255)->nullable(false)->change();
        });
    }
}
