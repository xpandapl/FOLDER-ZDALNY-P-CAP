<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockDatesTable extends Migration
{
    public function up()
    {
        Schema::create('block_dates', function (Blueprint $table) {
            $table->id();
            $table->date('block_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('block_dates');
    }
}