<?php

namespace App\Http\Controllers;

use App\Models\HourlyRateUpdate;
use App\Models\projectUser;
use App\Models\worksnapUser;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = worksnapUser::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%")
                    ->orWhere('email',      'like', "%{$search}%");
            });
        }

        $users = $query->whereNotNull('email')
            ->where('email', '<>', '')
            ->orderBy('last_name')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('users.index', compact('users', 'search'));
    }
    public function show(Request $request, $id){
        //search for a user by their id
        $user = worksnapUser::find($id);

        //projects associated with a user
        $projects = $user->projects;

        $projectUsers = projectUser::with('project')
            ->where('user_id', $user->id)
            ->get();

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

            return view('users.show',
                compact('user',
                'timmingsByDay',
                'overallAverageActivityLevel',
                'totalTime',
                'projects',
                'projectUsers'));
        } else {
            // Handle user not found scenario
            return abort(404);
        }
    }

    /**
     * Converts a given number of seconds into a formatted string representing hours and minutes.
     *
     * @param int $seconds The total number of seconds to be converted.
     *
     * @return string The formatted time interval as a string in the format "HH:MM".
     */
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

    /**
    * Bulk update hourly rates for a user across their projects,
    * recording each change in the hourly_rate_updates table.
    *
    * @param Request $request
    * @param int $userId
    * @return RedirectResponse
    */
    public function bulkUpdateHourlyRates(Request $request, int $userId)
    {
        $data = $request->validate([
            'rates'   => ['required','array'],
            'rates.*' => ['required','numeric','min:0'],
        ]);

        $projectUsers = projectUser::where('user_id', $userId)
            ->whereIn('project_id', array_keys($data['rates']))
            ->get();

        if ($projectUsers->isEmpty()) {
            return back()->with('error', 'No project assignments found for updates.');
        }

        $connection = $projectUsers->first()->getConnection();
        $connection->transaction(function() use ($projectUsers, $data) {
            foreach ($projectUsers as $pu) {
                $newRate = $data['rates'][$pu->project_id];
                if ($pu->hourly_rate != $newRate) {
                    $previous = $pu->hourly_rate;

                    $pu->hourly_rate = $newRate;
                    $pu->save();

                    HourlyRateUpdate::create([
                        'user_id'       => $pu->user_id,
                        'previous_rate' => $previous,
                        'new_rate'      => $newRate,
                    ]);
                }
            }
        });

        return back()->with('success', 'Hourly rates updated successfully.');
    }
}
