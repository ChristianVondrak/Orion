<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function show(Request $request, $id)
    {
        $projects = Project::all();
        $project = $projects->find($id);

        $startDate = $request->input('start');
        $endDate = $request->input('end');

        if (empty($startDate) && empty($endDate)) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $results = DB::table('worksnap_users AS wu')
            ->select(
                'wu.first_name',
                'wu.last_name',
                'pu.hourly_rate',
                DB::raw('COUNT(t.from_timestamp) * 10 AS Minutes_Worked')
            )
            ->join('project_users AS pu', 'pu.user_id', '=', 'wu.id')
            ->join('projects AS p', 'p.id', '=', 'pu.project_id')
            ->join('timmings AS t', 't.user_id', '=', 'wu.id')
            ->where('t.from_timestamp', '>=', DB::raw("UNIX_TIMESTAMP(CONVERT_TZ('$startDate', '+00:00', '-02:00'))"))
            ->where('t.from_timestamp', '<=', DB::raw("UNIX_TIMESTAMP(CONVERT_TZ('$endDate', '+00:00', '-02:00'))"))
            ->where('p.id', $id)
            ->groupBy('wu.first_name', 'wu.last_name', 'pu.hourly_rate')
            ->get();

//        Configure Cascade from Carbon for show only Hours and minutes
        CarbonInterval::setCascadeFactors([
            'minute' => [60, 'seconds'],
            'hour' => [60, 'minutes']
        ]);

        foreach ($results as $result) {
            $interval = CarbonInterval::minutes($result->Minutes_Worked)->cascade();
            $result->Minutes_Worked = $interval;
        }

        return view('projects.show', compact('project', 'startDate', 'endDate', 'results'));
    }
}
