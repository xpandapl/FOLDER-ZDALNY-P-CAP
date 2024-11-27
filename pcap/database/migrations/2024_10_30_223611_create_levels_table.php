<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('total_points')->default(1000);
            $table->integer('threshold')->default(80); // Próg zaliczenia w procentach
            $table->timestamps();
        });
        Schema::table('competencies', function (Blueprint $table) {
            $table->integer('points')->default(0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user');
        });
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('levels');
    
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('manager_username');
            $table->dropColumn('role');
        });
    }
    
}
