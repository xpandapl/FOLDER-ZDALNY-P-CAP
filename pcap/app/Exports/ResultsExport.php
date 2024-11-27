<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ResultsExport implements FromView
{
    protected $results;
    protected $team;

    public function __construct($results, $team)
    {
        $this->results = $results;
        $this->team = $team;
    }

    public function view(): View
    {
        return view('exports.results', [
            'results' => $this->results,
            'team' => $this->team
        ]);
    }
}
