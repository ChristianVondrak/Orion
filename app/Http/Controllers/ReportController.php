<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityIndexReportRequest;
use App\Services\Report;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
                'route' => route('reports.newcomers'),
            ],
            [
                'title' => 'Actualizaciones de Tarifas',
                'description' => 'Cambios en el hourly rate',
                'route' => route('reports.rateupdates'),
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
     *
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
}
