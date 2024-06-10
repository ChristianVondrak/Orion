<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(Request $request, $id)
    {
        // variables to filter between 2 dates
        $startDate = Carbon::now()->startOfMonth()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfMonth()->shiftTimezone('UTC')->timestamp;

        // If the filter comes by request, the given one is used
        if ($request->has('start') && $request->has('end')) {
            $startDate = Carbon::createFromFormat('Y/m/d', $request->input('start'))
                ->startOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
            $endDate = Carbon::createFromFormat('Y/m/d', $request->input('end'))
                ->endOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
        }

        // Retrieve a project with associated users and their work timings within a specified date range.
        $project = Project::with(['projectUsers.worknapUser.timmings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('from_timestamp', [$startDate, $endDate]);
        }])->find($id);

        return view('projects.show', compact('project', 'startDate', 'endDate'));
    }
}
