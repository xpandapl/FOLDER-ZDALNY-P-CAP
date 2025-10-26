<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hierarchy_structure', function (Blueprint $table) {
            // Zmień head_username na nullable
            $table->string('head_username')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('hierarchy_structure', function (Blueprint $table) {
            $table->string('head_username')->nullable(false)->change();
        });
    }
};