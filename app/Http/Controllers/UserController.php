<?php

namespace App\Http\Controllers;

use App\Models\worksnapUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request, $id){
        // variables to filter between 2 dates
        $startDate = Carbon::now()->startOfWeek()->shiftTimezone('UTC')->timestamp;
        $endDate = Carbon::now()->endOfWeek()->shiftTimezone('UTC')->timestamp;

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

        //search for a user by their id
        $user = worksnapUser::find($id);

        if ($user) {
            $perPage = 10;
            //search for timings associated with a user between 2 dates
            $timmings = $user->timmings()
                ->whereBetween('from_timestamp', [$startDate, $endDate])
                ->where('user_id', $id)
                //Add start and end parameters to pagination links to maintain these filters during page navigation.
                ->with('project')
                ->get();

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

            return view('users.show', compact('user', 'timmingsByDay'));
        } else {
            // Handle user not found scenario
            return abort(404);
        }
    }
}
