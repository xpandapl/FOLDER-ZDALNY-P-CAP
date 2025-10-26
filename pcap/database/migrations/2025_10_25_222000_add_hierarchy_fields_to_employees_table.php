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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('supervisor_username')->nullable()->after('manager_username');
            $table->string('head_username')->nullable()->after('supervisor_username');
            
            // Dodanie indeksów
            $table->index('supervisor_username');
            $table->index('head_username');
            
            // Dodanie kluczy obcych
            $table->foreign('supervisor_username')->references('username')->on('users')->onDelete('set null');
            $table->foreign('head_username')->references('username')->on('users')->onDelete('set null');
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
            // Usunięcie kluczy obcych
            $table->dropForeign(['supervisor_username']);
            $table->dropForeign(['head_username']);
            
            // Usunięcie kolumn
            $table->dropColumn(['supervisor_username', 'head_username']);
        });
    }
};