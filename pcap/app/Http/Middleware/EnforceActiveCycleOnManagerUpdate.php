<?php

namespace App\Http\Middleware;

use App\Models\AssessmentCycle;
use App\Models\Result;
use Closure;
use Illuminate\Http\Request;

class EnforceActiveCycleOnManagerUpdate
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure there is an active cycle
        $activeCycleId = AssessmentCycle::where('is_active', true)->value('id');
        if (!$activeCycleId) {
            return redirect()->back()->with('error', 'Brak aktywnego cyklu. Zapis zablokowany.');
        }

        // Filter posted result IDs to only those belonging to the active cycle
        $scores = (array) $request->input('score_manager', []);
        $feedbacks = (array) $request->input('feedback_manager', []);

        if (!empty($scores)) {
            $ids = array_map('intval', array_keys($scores));
            $results = Result::whereIn('id', $ids)->get(['id','cycle_id']);
            $allowedIds = $results->filter(fn($r) => (int)$r->cycle_id === (int)$activeCycleId)->pluck('id')->all();

            if (count($allowedIds) !== count($ids)) {
                // Drop any non-active results from the payload
                $filteredScores = array_intersect_key($scores, array_flip($allowedIds));
                $filteredFeedbacks = array_intersect_key($feedbacks, array_flip($allowedIds));
                $request->merge([
                    'score_manager' => $filteredScores,
                    'feedback_manager' => $filteredFeedbacks,
                ]);
                session()->flash('info', 'Zmiany w cyklach historycznych zostały zablokowane.');
            }
        }

        return $next($request);
    }
}
