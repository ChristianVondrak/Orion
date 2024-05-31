<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\projectUser;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfMonth()->shiftTimezone('UTC')->timestamp;

//      Count of timestamps associated with each project
        $projects = Project::withCount(['timmings' => function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween('from_timestamp', [$startDate, $endDate]);
        }])->get();

        return view('dashboard', compact('projects'));
    }
}
