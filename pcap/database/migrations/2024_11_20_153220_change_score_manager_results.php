<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeScoreManagerResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->decimal('score_manager', 5, 2)->change();
        });
    }
    
    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->integer('score_manager')->change();
        });
    }
}
