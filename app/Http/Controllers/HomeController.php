<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfMonth()->shiftTimezone('UTC')->timestamp;

        $status = request()->input('status');
        $query = Project::withCount(['timmings' => function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween('from_timestamp', [$startDate, $endDate]);
        }]);
        if ($status !== null && in_array($status, ['0','1'])) {
            $query->where('status', $status);
        }
        $projects = $query->get();

        return view('dashboard', compact('projects'));
    }
}
