<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefinitionColumnInCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('definition')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('definition')->nullable(false)->change();
        });
    }
    
}
