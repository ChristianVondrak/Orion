<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\projectUser;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Timming;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Class StatisticsController
 *
 * This controller provides various statistical queries for the HR dashboard,
 * including contractor compensation structure, company distribution, seniority ranges,
 * marital status by gender, department counts, and monthly project time tracking,
 * as well as a view to display the dashboard.
 *
 * @package App\Http\Controllers
 */
class StatisticsController extends Controller
{
    /**
     * Display the statistics dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.statistics');
    }

    /**
     * Get the number of contractors who receive a fixed monthly payment vs hourly rate.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function compensationStructure()
    {
        $fixed = projectUser::whereNull('hourly_rate')
                ->orWhere('hourly_rate', 0)
                ->count();

        $hourly = projectUser::where('hourly_rate', '>', 0)->count();

        return response()->json([
            'fixed' => $fixed,
            'hourly' => $hourly,
        ]);
    }

    /**
     * Get the number of contractors per company.
     *
     * @return \Illuminate\Support\Collection
     */
    public function contractorsPerCompany()
    {
        return Project::withCount('projectUsers as total')
            ->select('id', 'name')
            ->get();
    }

    /**
     * Get the distribution of contractors by seniority range in years.
     *
     * Ranges: 0-2, 3-5, 6-8, 9-11, 12-20, 21+ years
     *
     * @return \Illuminate\Support\Collection
     */
    public function contractorsSeniority()
    {
        return User::all()->mapToGroups(function ($user) {
            $years = now()->diffInYears($user->created_at);
            if ($years <= 2) return ['0-2' => 1];
            if ($years <= 5) return ['3-5' => 1];
            if ($years <= 8) return ['6-8' => 1];
            if ($years <= 11) return ['9-11' => 1];
            if ($years <= 20) return ['12-20' => 1];
            return ['21+' => 1];
        })->map(fn ($group) => $group->count());
    }

    /**
     * Get the total number of contractors by marital status and gender.
     *
     * @return \Illuminate\Support\Collection
     */
    public function maritalStatusByGender()
    {
        return UserDetail::select('gender', 'marital_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('gender', 'marital_status')
            ->get();
    }

    /**
     * Get the number of contractors per department.
     *
     * @return \Illuminate\Support\Collection
     */
    public function contractorsPerDepartment()
    {
        return UserDetail::select('position as department')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('position')
            ->get();
    }

    /**
     * Get the percentage of time worked per project in the current month,
     * relative to the monthly goal (160 hours).
     *
     * @return \Illuminate\Support\Collection
     */
    public function projectHourCompletion()
    {
        $month = now()->month;
        $year = now()->year;

        $projects = Timming::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select('project_id')
            ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, from_timestamp, to_timestamp)) as total_hours')
            ->groupBy('project_id')
            ->get()
            ->map(function ($row) {
                $row->percentage = round(($row->total_hours / Project::MONTH_HOURS_GOAL_VZLA) * 100, 2);
                return $row;
            });

        return $projects;
    }
}


