<?php

namespace App\Services;

use App\Models\Project;
use App\Models\worksnapUser;
use App\Models\Timming;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceService
{
    /**
     * Genera la colección de invoices de un proyecto hasta $cutoff.
     *
     * @param  int    $projectId
     * @param  Carbon $cutoffDate
     * @return Collection
     */
    public function getProjectInvoices(int $projectId, Carbon $cutoffDate): Collection
    {
        $startTs = $cutoffDate->copy()->startOfMonth()->timestamp;
        $endTs   = $cutoffDate->timestamp;

        $workers = worksnapUser::whereHas(
            'projects',
            fn($q) => $q->where('project_id', $projectId)
        )->with([
            'projects' => fn($q) => $q->where('project_id', $projectId)
        ])->get();

        $invoices = collect();

        foreach ($workers as $w) {
            // 1) obtener timmings de este proyecto en el rango
            $timings = Timming::where('project_id', $projectId)
                ->where('user_id', $w->id)
                ->whereBetween('from_timestamp', [$startTs, $endTs])
                ->get();

            // 2) agrupar por día
            $grouped = $timings->groupBy(fn($t) =>
            Carbon::createFromTimestamp($t->from_timestamp)
                ->format('Y-m-d')
            );

            $rate    = $w->projects->first()->pivot->hourly_rate;
            $daily   = [];
            $subtotal = 0;

            foreach ($grouped as $date => $group) {
                $hours  = round($group->count() * 10 / 60, 2);
                $amount = round($hours * $rate, 2);
                $daily[] = compact('date', 'hours', 'rate', 'amount');
                $subtotal += $amount;
            }

            // 3) activity index
            $activityIndex = $timings->avg('activity_level') * 10;

            // 4) ajuste automático
            $auto = 0;
            if ($activityIndex > 75 && $activityIndex < 85) {
                $auto = round($subtotal * 0.1, 2);
            }
            elseif ($activityIndex > 85) {
                $auto = round($subtotal * 0.2, 2);
            }
            elseif ($activityIndex < 75) {
                $auto = round(-$subtotal * 0.1, 2);
            }

            $invoices->push([
                'user'               => $w,
                'daily'              => $daily,
                'subtotal'           => round($subtotal, 2),
                'auto_adjustment'    => $auto,
                'manual_adjustment'  => 0.00,
                'activity_index'     => round($activityIndex, 2),
                'period'             => $cutoffDate->format('F Y'),
                'url'                => route('project.show', $projectId),
            ]);
        }

        return $invoices;
    }

    /**
     * Envía por mail todos los invoices generados, aplicando los
     * ajustes manuales que vienen en $manuals[user_id] => monto.
     *
     * @param  int    $projectId
     * @param  Carbon $cutoffDate
     * @param  array  $manuals
     * @return void
     */
    public function sendInvoices(int $projectId, Carbon $cutoffDate, array $manuals): void
    {
        Log::info("sendInvoices arrancó — projectId={$projectId}, corte={$cutoffDate->toDateString()}");
        $invoices = $this->getProjectInvoices($projectId, $cutoffDate);

        foreach ($invoices as $inv) {
            $userId = $inv['user']->id;

            $inv['manual_adjustment'] = round($manuals[$userId] ?? 0, 2);
            $inv['total'] = round(
                $inv['subtotal']
                + $inv['auto_adjustment']
                + $inv['manual_adjustment'],
                2);

            if (app()->environment('local') && count($invoices) > 0) {
                Log::info("ENTRO AL IF");
                $inv = $invoices[0]; // único
                try {
                Mail::to('christianvondrak99@gmail.com')
                    ->send(new InvoiceMail($inv));
                    Log::info("InvoiceMail enviado para user_id={$inv['user']->id}");
                } catch (\Exception $e) {
                    // Lo registramos en el log y devolvemos algo para debug
                    Log::error("Error enviando InvoiceMail a {$inv['user']->email}: ".$e->getMessage());
                    // opcionalmente:
                    throw $e;
                }

                return;
            }
            //DESCOMENTAR CUANDO SE TERMINE LA PRESENTACION DE LA TESIS
/*            Mail::to($inv['user']->email)
                ->send(new InvoiceMail($inv));*/
        }
    }
}
