<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentsToResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->text('comments')->nullable(); // Dodajemy kolumnę 'comments', która może być pusta
        });
    }
    
    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('comments'); // Usuwamy kolumnę 'comments' w razie cofnięcia migracji
        });
    }
    
}
