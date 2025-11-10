<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('block_dates', function (Blueprint $table) {
            $table->dateTime('block_new_submissions_date')->nullable()->after('block_date')->comment('Data blokady dla nowych formularzy (świeżacy)');
            $table->dateTime('block_edits_date')->nullable()->after('block_new_submissions_date')->comment('Data blokady dla edycji istniejących formularzy (weterani)');
        });
        
        // Migruj starą wartość block_date do nowych kolumn jeśli istnieje
        $blockDate = DB::table('block_dates')->first();
        if ($blockDate && $blockDate->block_date) {
            DB::table('block_dates')->update([
                'block_new_submissions_date' => $blockDate->block_date,
                'block_edits_date' => $blockDate->block_date,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_dates', function (Blueprint $table) {
            $table->dropColumn(['block_new_submissions_date', 'block_edits_date']);
        });
    }
};
