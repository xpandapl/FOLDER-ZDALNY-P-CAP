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
            // Pozwalamy na NULL dla manager_username (pracownicy bezpośrednio pod headem)
            $table->string('manager_username')->nullable()->change();
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
            // Przywracamy NOT NULL constraint
            $table->string('manager_username')->nullable(false)->change();
        });
    }
};