<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Team;

class AddNewTeamsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Dodaj nowe działy do tabeli teams
        $newTeams = [
            'Expo Designer',
            'Sales Innovation PM',
            'NPI',
            'Production Support'
        ];

        foreach ($newTeams as $teamName) {
            Team::firstOrCreate(['name' => $teamName]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Usuń dodane działy
        $teamsToRemove = [
            'Expo Designer',
            'Sales Innovation PM',
            'NPI',
            'Production Support'
        ];

        Team::whereIn('name', $teamsToRemove)->delete();
    }
}
