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
        $startDate = Carbon::now()->startOfMonth()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfMonth()->shiftTimezone('UTC')->timestamp;

        if ($request->has('start') && $request->has('end')) {
            $startDate = Carbon::createFromFormat('Y/m/d', $request->input('start'))->shiftTimezone('UTC')->timestamp;
            $endDate = Carbon::createFromFormat('Y/m/d', $request->input('end'))->shiftTimezone('UTC')->timestamp;
        }

        $project = Project::with(['projectUsers.worknapUser.timmings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('from_timestamp', [$startDate, $endDate]);
        }])->find($id);



//        $results = DB::table('worksnap_users AS wu')
//            ->select(
//                'wu.first_name',
//                'wu.last_name',
//                'pu.hourly_rate',
//                DB::raw('COUNT(t.from_timestamp) * 10 AS Minutes_Worked')
//            )
//            ->join('project_users AS pu', 'pu.user_id', '=', 'wu.id')
//            ->join('projects AS p', 'p.id', '=', 'pu.project_id')
//            ->join('timmings AS t', 't.user_id', '=', 'wu.id')
//            ->where('t.from_timestamp', '>=',$startDate)
//            ->where('t.from_timestamp', '<=',$endDate)
//            ->where('p.id', $id)
//            ->groupBy('wu.first_name', 'wu.last_name', 'pu.hourly_rate')
//            ->get();

//        Configure Cascade from Carbon for show only Hours and minutes
        CarbonInterval::setCascadeFactors([
            'minute' => [60, 'seconds'],
            'hour' => [60, 'minutes']
        ]);

//        foreach ($results as $result) {
//            $interval = CarbonInterval::minutes($result->Minutes_Worked)->cascade();
//            $result->Minutes_Worked = $interval;
//        }

        return view('projects.show', compact('project', 'startDate', 'endDate'));
    }
}
