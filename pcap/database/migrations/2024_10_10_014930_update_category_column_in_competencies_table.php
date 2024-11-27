<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoryColumnInCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('category')->change(); // Zmiana typu kolumny category na TEXT
        });
    }
    
    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->string('category', 255)->change(); // Przywrócenie pierwotnego stanu w razie cofnięcia migracji
        });
    }    
}
