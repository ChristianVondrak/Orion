<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait HandlesReports
{
    /**
     * Serve a report with optional Excel/PDF export and HTML pagination.
     *
     * @param  mixed       $request           Must expose getRange() and exportType()
     * @param  callable    $paginatedDataFn   fn(start, end, perPage): LengthAwarePaginator
     * @param  callable    $allDataFn         fn(start, end): array|\Illuminate\Support\Collection
     * @param  string      $view              Blade view for HTML output
     * @param  string      $excelExport       Excel export class
     * @param  string|null $pdfView           Blade view for PDF (null = no PDF)
     * @param  string      $filenamePrefix    Filename prefix (no extension)
     * @param  array       $viewData          Extra data to pass into the view/PDF
     * @param  int|null    $perPage           Items per page for HTML (null = no pagination)
     * @return Response|BinaryFileResponse
     */
    protected function serveReport(
        $request,
        callable $paginatedDataFn,
        callable $allDataFn,
        string $view,
        string $excelExport,
        ?string $pdfView,
        string $filenamePrefix,
        array $viewData = [],
        ?int $perPage = 15
    ): Response|BinaryFileResponse {
        ['start' => $start, 'end' => $end] = $request->getRange();
        $export = $request->exportType();

        // 1) Excel export
        if ($export === 'excel') {
            $allRows = call_user_func($allDataFn, $start, $end);
            return Excel::download(
                new $excelExport($allRows),
                "{$filenamePrefix}.xlsx"
            );
        }

        // 2) PDF export
        if ($export === 'pdf' && $pdfView) {
            $allRows = call_user_func($allDataFn, $start, $end);
            $payload = array_merge($viewData, [
                'rows'  => $allRows,
                'start' => $start->format('Y/m/d'),
                'end'   => $end->format('Y/m/d'),
            ]);

            $pdf = Pdf::loadView($pdfView, $payload)
                ->setPaper('a4', 'landscape');

            return $pdf->download("{$filenamePrefix}.pdf");
        }

        // 3) HTML view (paginated or full collection)
        $result = call_user_func($paginatedDataFn, $start, $end, $perPage);

        if ($result instanceof LengthAwarePaginator) {
            $rows = $result->appends($request->only(['start','end','year','project_id']));
        } else {
            $rows = $result;
        }

        $data = array_merge($viewData, [
            'rows'  => $rows,
            'start' => $start,
            'end'   => $end,
        ]);

        return response()->view($view, $data);
    }
}
