<?php

namespace App\Http\Controllers;

use App\Models\worksnapUser;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request, $id){
        //search for a user by their id
        $user = worksnapUser::find($id);

        //projects associated with a user
        $projects = $user->projects;

        // variables to filter between 2 dates
        $startDate = Carbon::now()->startOfMonth()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfMonth()->shiftTimezone('UTC')->timestamp;

        // ProjectId in case of filter per project
        $projectId = $request->query('project_id');

        // If the filter comes by request, the given one is used
        if ($request->has('start') && $request->has('end')) {
            $startDate = Carbon::createFromFormat('Y/m/d', $request->query('start'))
                ->startOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
            $endDate = Carbon::createFromFormat('Y/m/d', $request->query('end'))
                ->endOfDay()
                ->shiftTimezone('UTC')
                ->timestamp;
        }

        if ($user) {
            //search for timings associated with a user between 2 dates
            $query = $user->timmings()
                ->whereBetween('from_timestamp', [$startDate, $endDate]);
            //if project filter exist
            if ($projectId) {
                $query->where('project_id', $projectId);
            }

            $timmings = $query->with('project')->get();

            $timmingsByDay = $timmings->groupBy(function($item) {
                return Carbon::createFromTimestamp($item->from_timestamp)->format('Y-m-d');
            })->map(function($dayGroup) {
                // 10 minutes in seconds
                $totalSeconds = $dayGroup->count() * 10 * 60;
                // projects associated at daily timing
                $projects = $dayGroup->pluck('project')->unique('id');
                // task associated at daily timing
                $taskNames = $dayGroup->pluck('task_name')->unique();
                // avg of activity level of the day
                $averageActivityLevel = $dayGroup->avg('activity_level');

                return [
                    'total_seconds' => $totalSeconds,
                    'projects' => $projects,
                    'task_names' => $taskNames,
                    'average_activity_level' => $averageActivityLevel,
                ];
            });

            //totalizations of hours and activity lvel
            $totalTime = $timmingsByDay->sum('total_seconds');
            $overallAverageActivityLevel = $timmings->avg('activity_level');
            //Convert seconds in hours and apply format (h:i)
            $totalTime = $this->convertSecondsInHours($totalTime);

            return view('users.show', compact('user', 'timmingsByDay', 'overallAverageActivityLevel', 'totalTime', 'projects'));
        } else {
            // Handle user not found scenario
            return abort(404);
        }
    }

    public function convertSecondsInHours($seconds)
    {
        CarbonInterval::setCascadeFactors([
            'minute' => [60, 'seconds'],
            'hour' => [60, 'minutes'],
            // in this example the cascade won't go farther than week unit
        ]);

        $totalTimeInterval = CarbonInterval::seconds($seconds)->cascade();
        return $totalTimeInterval->format('%H:%I');
    }
}
