<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValueToCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->integer('value')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
    
}
