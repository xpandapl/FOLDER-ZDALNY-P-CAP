<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionsToCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('description_075_to_1')->nullable(); // Description for score 0.75 to 1
            $table->text('description_0_to_05')->nullable(); // Description for score 0 to 0.5
            $table->text('description_above_expectations')->nullable(); // Description for above expectations
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
            $table->dropColumn('description_075_to_1');
            $table->dropColumn('description_0_to_05');
            $table->dropColumn('description_above_expectations');
        });
    }
}
