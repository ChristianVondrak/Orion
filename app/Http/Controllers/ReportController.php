<?php

namespace App\Http\Controllers;

use App\Exports\ActivityIndexExport;
use App\Exports\HourlyRateUpdatesExport;
use App\Exports\NewHiresExport;
use App\Exports\TerminationsExport;
use App\Exports\LoginReportExport;
use App\Exports\AnnualHoursExport;
use App\Http\Requests\RateUpdatesRequest;
use App\Http\Requests\ReportRangeRequest;
use App\Http\Requests\AnnualHoursRequest;
use App\Services\Report;
use App\Services\AnnualHoursReportService;
use App\Models\Project;
use App\Http\Controllers\Traits\HandlesReports;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    use HandlesReports;

    /**
     * Show the list of available reports.
     */
    public function index(): Response
    {
        $reports = [
            ['title' => 'Login by Professional',   'description' => 'Login delays and session schedule',   'route' => route('reports.login')],
            ['title' => 'Activity Index',          'description' => 'WorkSnaps activity index',             'route' => route('reports.activity')],
            ['title' => 'New Hires',               'description' => 'Tracking of new professionals',       'route' => route('reports.newHires')],
            ['title' => 'Rate Updates',            'description' => 'Changes in hourly rate',              'route' => route('reports.rateupdates')],
            ['title' => 'Contractor Terminations', 'description' => 'Tracking of contractor terminations', 'route' => route('reports.terminations')],
            ['title' => 'Annual Hours',            'description' => 'Annual hours per professional',       'route' => route('reports.annualHours')],
        ];

        return response()->view('reports.index', compact('reports'));
    }

    /**
     * Activity Index report (paginated & exportable).
     */
    public function activityIndex(ReportRangeRequest $request, Report $service): Response
    {
        return $this->serveReport(
            $request,
            [$service, 'getActivityIndexData'],       // paginated
            [$service, 'getAllActivityIndexData'],    // full data for export
            'reports.activity',                       // HTML view
            ActivityIndexExport::class,               // Excel export class
            'reports.activity_pdf',                   // PDF view
            'activity-index'                          // filename prefix
        );
    }

    /**
     * Hourly Rate Updates report.
     */
    public function rateUpdates(RateUpdatesRequest $request, Report $service): Response
    {
        $year = $request->getYear();

        return $this->serveReport(
            $request,
            [$service, 'getHourlyRateUpdatesData'],
            [$service, 'getAllHourlyRateUpdatesData'],
            'reports.rate_updates',
            HourlyRateUpdatesExport::class,
            'reports.rate_updates_pdf',
            'hourly-rate-updates',
            [
                'year' => $year,
            ],
        );
    }

    /**
     * New Hires report.
     */
    public function newHires(ReportRangeRequest $request, Report $service): Response
    {
        return $this->serveReport(
            $request,
            [$service, 'getNewUsersData'],
            [$service, 'getAllNewUsersData'],
            'reports.new_hires',
            NewHiresExport::class,
            'reports.new_hires_pdf',
            'new-hires'
        );
    }

    /**
     * Contractor Terminations report.
     */
    public function terminations(ReportRangeRequest $request, Report $service): Response
    {
        return $this->serveReport(
            $request,
            [$service, 'getTerminationsData'],
            [$service, 'getAllTerminationsData'],
            'reports.terminations',
            TerminationsExport::class,
            'reports.terminations_pdf',
            'terminations'
        );
    }

    /**
     * Login report.
     */
    public function login(ReportRangeRequest $request, Report $service): Response
    {
        return $this->serveReport(
            $request,
            [$service, 'getLoginReportData'],
            [$service, 'getAllLoginReportData'],
            'reports.login',
            LoginReportExport::class,
            'reports.login_pdf',
            'login-report'
        );
    }

    /**
     * Annual Hours report (no pagination, exportable).
     */
    public function annualHours(AnnualHoursRequest $request, AnnualHoursReportService $service): Response
    {
        $year      = $request->getYear();
        $projectId = $request->getProjectId();

        $allData  = $service->getAnnualHoursData($year, $projectId);
        $projects = Project::select('id', 'name')->orderBy('name')->get();

        return $this->serveReport(
            $request,
            fn($start, $end, $perPage) => new LengthAwarePaginator(
                $allData,
                $allData->count(),
                $allData->count(),
                1,
                ['path' => url()->current(), 'query' => request()->query()]
            ),
            fn($start, $end) => $allData,
            'reports.annual_hours',
            AnnualHoursExport::class,
            'reports.annual_hours_pdf',
            "annual-hours-{$year}",
            [
                'view'            => 'reports.annual_hours',
                'data'            => $allData,
                'year'            => $year,
                'projects'        => $projects,
                'selectedProject' => $projectId,
            ],
            null
        );
    }
}
