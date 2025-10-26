<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHierarchyStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hierarchy_structure', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('supervisor_username');
            $table->string('manager_username');
            $table->string('head_username');
            $table->timestamps();
            
            // Indexes
            $table->index('department');
            $table->index('supervisor_username');
            $table->unique(['department', 'supervisor_username']);
            
            // Foreign keys
            $table->foreign('supervisor_username')->references('username')->on('users')->onDelete('cascade');
            $table->foreign('manager_username')->references('username')->on('users')->onDelete('cascade');
            $table->foreign('head_username')->references('username')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hierarchy_structure');
    }
}
