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
        // Modyfikujemy tabelę employees aby obsługiwała elastyczną hierarchię
        Schema::table('employees', function (Blueprint $table) {
            // Dodajemy pole immediate_supervisor - bezpośredni przełożony
            $table->string('immediate_supervisor_username')->nullable()->after('manager_username');
            
            // Dodajemy pole team_name dla lepszego grupowania
            $table->string('team_name')->nullable()->after('department');
            
            // Indeksy
            $table->index('immediate_supervisor_username');
            $table->index('team_name');
            
            // Klucz obcy
            $table->foreign('immediate_supervisor_username')->references('username')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['immediate_supervisor_username']);
            $table->dropColumn(['immediate_supervisor_username', 'team_name']);
        });
    }
};