<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizational_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // np. "SEO", "E-commerce Performance", "Brand Marketing"
            $table->string('department'); // np. "Growth"
            $table->string('team_leader_username'); // Bezpośredni lider zespołu
            $table->string('team_leader_role'); // supervisor, manager, head
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indeksy
            $table->index('department');
            $table->index('team_leader_username');
            $table->unique(['name', 'department']);
            
            // Klucze obce
            $table->foreign('team_leader_username')->references('username')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizational_teams');
    }
};