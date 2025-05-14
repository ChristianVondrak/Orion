<?php

namespace App\Http\Controllers;

use App\Models\PlannedProjectHour;
use App\Models\Project;
use Illuminate\Http\Request;

class PlannedProjectHourController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'week_start'    => 'required|date',
            'planned_hours' => 'required|numeric|min:0',
        ]);

        PlannedProjectHour::updateOrCreate(
            ['project_id'=>$project->id,'week_start'=>$data['week_start']],
            ['planned_hours'=>$data['planned_hours']]
        );

        return back()->with('success','Horas planificadas actualizadas.');
    }
}
