<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedFullCodeToEmployeeCycleAccessCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_cycle_access_codes', function (Blueprint $table) {
            $table->text('encrypted_full_code')->nullable()->after('code_hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_cycle_access_codes', function (Blueprint $table) {
            $table->dropColumn('encrypted_full_code');
        });
    }
}
