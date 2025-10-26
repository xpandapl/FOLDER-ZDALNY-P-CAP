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
        Schema::table('hierarchy_structure', function (Blueprint $table) {
            // Pozwalamy na NULL dla supervisor_username (pracownicy bezpośrednio pod managerem)
            $table->string('supervisor_username')->nullable()->change();
            
            // Dodajemy pole team_name dla lepszego rozróżnienia zespołów w dziale
            $table->string('team_name')->nullable()->after('department');
            
            // Usuwamy unique constraint na department+supervisor, bo może być wiele struktur w dziale
            $table->dropUnique(['department', 'supervisor_username']);
            
            // Dodajemy nowy unique constraint na department+team_name
            $table->unique(['department', 'team_name']);
            
            $table->index('team_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hierarchy_structure', function (Blueprint $table) {
            $table->dropUnique(['department', 'team_name']);
            $table->dropColumn('team_name');
            $table->string('supervisor_username')->nullable(false)->change();
            $table->unique(['department', 'supervisor_username']);
        });
    }
};