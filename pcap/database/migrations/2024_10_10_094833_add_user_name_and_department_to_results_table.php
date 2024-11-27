<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserNameAndDepartmentToResultsTable extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->string('user_name')->nullable(); // Nowa kolumna na imię użytkownika
            $table->string('department')->nullable(); // Nowa kolumna na dział użytkownika
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('user_name');
            $table->dropColumn('department');
        });
    }
}
