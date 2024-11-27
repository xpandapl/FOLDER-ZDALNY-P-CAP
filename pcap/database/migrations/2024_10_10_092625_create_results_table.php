<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Id użytkownika
            $table->foreignId('competency_id')->constrained()->onDelete('cascade'); // Id kompetencji
            $table->integer('score')->nullable(); // Ocena użytkownika
            $table->boolean('above_expectations')->default(false); // Czy powyżej oczekiwań (użytkownik)
            $table->text('feedback_manager')->nullable(); // Feedback od managera
            $table->integer('score_manager')->nullable(); // Ocena managera
            $table->boolean('above_expectations_manager')->default(false); // Czy powyżej oczekiwań (manager)
            $table->timestamps(); // Znaczniki czasu
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
