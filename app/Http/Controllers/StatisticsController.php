<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\projectUser;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Timming;
use App\Models\worksnapUser;
use App\Models\PlannedProjectHour;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function contractorsPerCompany()
    {
        $projects = Project::withCount(['projectUsers', 'worksnapUsers'])
            ->select('id', 'name')
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name ?: 'Proyecto sin nombre',
                    'total' => $project->projectUsers()->count(),
                    'has_hourly_rates' => $project->projectUsers()
                        ->where('hourly_rate', '>', 0)
                        ->exists()
                ];
            });

        return response()->json($projects);
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
        $users = worksnapUser::select('id', 'email', 'created_at')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('created_at')
            ->get();

        $ranges = [
            '0-2' => 0,
            '3-5' => 0,
            '6-8' => 0,
            '9-11' => 0,
            '12-20' => 0,
            '21+' => 0
        ];

        foreach ($users as $user) {
            try {
                $years = Carbon::parse($user->created_at)->diffInYears(Carbon::now());
                
                if ($years <= 2) {
                    $ranges['0-2']++;
                } elseif ($years <= 5) {
                    $ranges['3-5']++;
                } elseif ($years <= 8) {
                    $ranges['6-8']++;
                } elseif ($years <= 11) {
                    $ranges['9-11']++;
                } elseif ($years <= 20) {
                    $ranges['12-20']++;
                } else {
                    $ranges['21+']++;
                }
            } catch (\Exception $e) {
                \Log::error("Error calculando antigüedad para usuario {$user->id}: " . $e->getMessage());
                continue;
            }
        }

        return collect($ranges);
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
     * Get the number of contractors per position.
     *
     * @return \Illuminate\Support\Collection
     */
    public function contractorsPerPosition()
    {
        return UserDetail::select('position')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('position')
            ->get();
    }

    /**
     * Get the percentage of time worked per project in the current month,
     * relative to the planned hours.
     *
     * @return \Illuminate\Support\Collection
     */
    public function projectHourCompletion()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return Project::with('projectUsers')
            ->whereHas('projectUsers') // Solo proyectos que tengan usuarios asignados
            ->get()
            ->map(function ($project) use ($start, $end) {
                // Obtener horas planificadas
                $plannedHours = PlannedProjectHour::getForWeek($project->id, $start) ?: 
                    ($project->projectUsers->count() * 160); // Default: 160h por usuario

                // Calcular horas reales basadas en bloques de 10 minutos
                $seconds = $project->timmings()
                    ->whereBetween('from_timestamp', [$start->timestamp, $end->timestamp])
                    ->count() * 10 * 60;
                $actualHours = round($seconds / 3600, 2);

                // Calcular porcentaje y estado
                $percentage = $plannedHours > 0 
                    ? min(100, round(($actualHours / $plannedHours) * 100, 2))
                    : 0;

                return [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'total_hours' => $actualHours,
                    'planned_hours' => $plannedHours,
                    'percentage' => $percentage,
                    'status' => $this->getProjectStatus($actualHours, $plannedHours)
                ];
            });
    }

    /**
     * Get the status of a project based on its hours completion
     *
     * @param float $actual
     * @param float $planned
     * @return string
     */
    private function getProjectStatus($actual, $planned)
    {
        if ($planned <= 0) return 'warning';
        
        $percentage = ($actual / $planned) * 100;
        // Usando los mismos umbrales que en AlertService (10% de desviación)
        if ($percentage >= 90 && $percentage <= 110) return 'on-track';
        if ($percentage >= 70 && $percentage < 90) return 'warning';
        if ($percentage > 110) return 'over';
        return 'behind';
    }
}


