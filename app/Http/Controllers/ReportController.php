<?php

namespace App\Http\Controllers;

use App\Exports\HourlyRateUpdatesExport;
use App\Exports\NewHiresExport;
use App\Http\Requests\ActivityIndexReportRequest;
use App\Models\HourlyRateUpdate;
use App\Services\Report;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $reports = [
            [
                'title' => 'Login por Profesional',
                'description' => 'Retrasos de entrada y horario de sesión',
                'route' => route('reports.login'),
            ],
            [
                'title' => 'Activity Index',
                'description' => 'Índice de actividad de WorkSnaps',
                'route' => route('reports.activity'),
            ],
            [
                'title' => 'Nuevos Ingresos',
                'description' => 'Seguimiento de nuevos profesionales',
                'route' => route('reports.newHires'),
            ],
            [
                'title' => 'Actualizaciones de Tarifas',
                'description' => 'Cambios en el hourly rate',
                'route' => route('reports.rateupdates'),
            ],
            [
                'title' => 'Destitucion de profesionales independientes',
                'description' => 'Seguimiento de los profesionales independientes',
                'route' => route('reports.terminations'),
            ],
        ];

        return view('reports.index', compact('reports'));
    }

    /**
     * Display the Activity Index report, with optional filtering and export.
     *
     * This method handles:
     *  1. Parsing 'start' and 'end' date parameters from the request, defaulting
     *     to the first and last day of the current month if missing or invalid.
     *  2. Exporting the full dataset to Excel or PDF when the 'export' query
     *     parameter is set to 'excel' or 'pdf'.
     *  3. Paginating the result set for normal HTML display when no export is
     *     requested.
     *
     * @param Request $request
     * @param Report $reportService
     * @return Application|Factory|View|\Illuminate\Foundation\Application|Response|\Illuminate\View\View|BinaryFileResponse
     */
    public function activityIndex(Request $request, Report $reportService)
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        if ($request->filled('start') && $request->filled('end')) {
            try {
                $start = Carbon::parse($request->query('start'))->startOfDay();
            } catch (\Exception $e) {
                $start = Carbon::now()->startOfMonth();
            }
            try {
                $end = Carbon::parse($request->query('end'))->endOfDay();
            } catch (\Exception $e) {
                $end = Carbon::now()->endOfMonth();
            }
        }

        if ($request->export === 'excel') {
            $allRows = $reportService->getAllActivityIndexData($start, $end);
            return Excel::download(
                new \App\Exports\ActivityIndexExport($allRows->toArray()),
                'activity-index_'.$start->format('Ymd').'-'.$end->format('Ymd').'.xlsx'
            );
        }
        if ($request->export === 'pdf') {
            $allRows = $reportService->getAllActivityIndexData($start, $end);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.activity_pdf', [
                'rows'  => $allRows,
                'start' => $start->format('Y/m/d'),
                'end'   => $end->format('Y/m/d'),
            ])->setPaper('a4','landscape');
            return $pdf->download('activity-index_'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
        }

        $rows = $reportService
            ->getActivityIndexData($start, $end, 15)
            ->appends($request->only(['start','end']));

        return view('reports.activity', compact('rows','start','end'));
    }

    /**
     * Display or export the Hourly Rate Updates report.
     *
     * @param Request $request
     * @param Report $reportService
     * @return Application|Factory|View|\Illuminate\Foundation\Application|Response|\Illuminate\View\View|BinaryFileResponse
     */
    public function rateUpdates(Request $request, Report $reportService)
    {
        // 1) Parse date range (default: current year)
        $start = Carbon::now()->startOfYear();
        $end   = Carbon::now()->endOfYear();

        if ($request->filled('year')) {
            $year = intval($request->year);
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();
        }
        if ($request->filled('start') && $request->filled('end')) {
            try {
                $start = Carbon::parse($request->start)->startOfDay();
                $end   = Carbon::parse($request->end)->endOfDay();
            } catch (\Exception $e) {
                // ignore invalid, keep year-range
            }
        }

        // 2) Export to Excel?
        if ($request->query('export') === 'excel') {
            $allRows = $reportService->getAllHourlyRateUpdatesData($start, $end);
            return Excel::download(
                new HourlyRateUpdatesExport($allRows->toArray()),
                'hourly-rate-updates_'.$start->format('Ymd').'-'.$end->format('Ymd').'.xlsx'
            );
        }

        // 3) Export to PDF?
        if ($request->query('export') === 'pdf') {
            $allRows = $reportService->getAllHourlyRateUpdatesData($start, $end);
            $pdf = Pdf::loadView('reports.rate_updates_pdf', [
                'rows'  => $allRows,
                'start' => $start->format('Y/m/d'),
                'end'   => $end->format('Y/m/d'),
            ])->setPaper('a4','landscape');

            return $pdf->download('hourly-rate-updates_'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
        }

        // 4) Otherwise: paginate and render view
        $rows = $reportService
            ->getHourlyRateUpdatesData($start, $end, 15)
            ->appends($request->only(['year','start','end']));

        return view('reports.rate_updates', [
            'rows'  => $rows,
            'start' => $start->format('Y/m/d'),
            'end'   => $end->format('Y/m/d'),
            'year'  => $request->year ?? $start->year,
        ]);
    }

    /**
     * Show or export the “New Hires” report based on worksnapUser.created_at.
     *
     * @param Request $request
     * @param Report $reportService
     * @return Application|Factory|View|\Illuminate\Foundation\Application|Response|\Illuminate\View\View|BinaryFileResponse
     */
    public function newHires(Request $request, Report $reportService)
    {
        // Defaults: first and last day of current month
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        // Override if valid range passed
        if ($request->filled('start') && $request->filled('end')) {
            try {
                $start = Carbon::parse($request->start)->startOfDay();
                $end   = Carbon::parse($request->end)->endOfDay();
            } catch (\Exception $e) {
                // keep defaults
            }
        }

        // Excel export?
        if ($request->query('export') === 'excel') {
            $all = $reportService->getAllNewUsersData($start, $end);
            return Excel::download(
                new NewHiresExport($all->toArray()),
                "new-hires_{$start->format('Ymd')}-{$end->format('Ymd')}.xlsx"
            );
        }

        // PDF export?
        if ($request->query('export') === 'pdf') {
            $all = $reportService->getAllNewUsersData($start, $end);
            $pdf = Pdf::loadView('reports.new_hires_pdf', [
                'rows'  => $all,
                'start' => $start->format('Y/m/d'),
                'end'   => $end->format('Y/m/d'),
            ])->setPaper('a4','landscape');

            return $pdf->download("new-hires_{$start->format('Ymd')}-{$end->format('Ymd')}.pdf");
        }

        // Otherwise paginate & render
        $rows = $reportService
            ->getNewUsersData($start, $end, 15)
            ->appends($request->only(['start','end']));

        return view('reports.new_hires', [
            'rows'  => $rows,
            'start' => $start->format('Y/m/d'),
            'end'   => $end->format('Y/m/d'),
        ]);
    }

    public function terminations(Request $request, Report $reportService)
    {
        // Defaults: first and last day of current month
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        // Override if valid range passed
        if ($request->filled('start') && $request->filled('end')) {
            try {
                $start = Carbon::parse($request->start)->startOfDay();
                $end   = Carbon::parse($request->end)->endOfDay();
            } catch (\Exception $e) {
                // keep defaults
            }
        }

        if ($request->export==='excel') {
            $all = $reportService->getAllTerminationsData($start,$end);
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\TerminationsExport($all->toArray()),
                "terminations_{$start->format('Ymd')}-{$end->format('Ymd')}.xlsx"
            );
        }
        if ($request->export==='pdf') {
            $all = $reportService->getAllTerminationsData($start,$end);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.terminations_pdf', [
                'rows'=>$all,'start'=>$start->format('Y/m/d'),'end'=>$end->format('Y/m/d'),
            ])->setPaper('a4','landscape');
            return $pdf->download("terminations_{$start->format('Ymd')}-{$end->format('Ymd')}.pdf");
        }

        $rows = $reportService->getTerminationsData($start,$end,15)
            ->appends($request->only(['start','end']));

        return view('reports.terminations', compact('rows','start','end'));
    }
}
