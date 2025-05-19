<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceController extends Controller
{
    public function preview(Request $request, Project $project, InvoiceService $svc)
    {
        $cutoff   = Carbon::now();
        $all      = $svc->getProjectInvoices($project->id, $cutoff);

        // 1) Filtrar por búsqueda
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $all = $all->filter(fn($inv) =>
            str_contains(
                strtolower($inv['user']->first_name.' '.$inv['user']->last_name),
                strtolower($search)
            )
            );
        }

        // 2) Paginación manual de la colección
        $perPage = 9;
        $page    = $request->input('page', 1);
        $items   = $all->forPage($page, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $page,
            [
                'path'  => route('project.invoices.preview', $project->id),
                'query' => $request->only('search'),
            ]
        );

        return view('invoices.preview', [
            'project'    => $project,
            'invoices'   => $paginator,
            'cutoff'     => $cutoff,
            'search'     => $search,
        ]);
    }

    public function send(Request $request, Project $project, InvoiceService $svc)
    {
        $cutoff = Carbon::now();
        $manuals = $request->input('manual_adjustments', []);

        $svc->sendInvoices($project->id, $cutoff, $manuals);

        return redirect()
            ->route('project.show', $project->id)
            ->with('success', 'Invoices enviados correctamente.');
    }
}
