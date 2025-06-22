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
use App\Services\WhatsAppService;

class InvoiceService
{
    public function __construct(protected WhatsAppService $whatsapp)
    {
        //
    }

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
            $projectUser = $w->projects->first()->pivot;
            $isFlat = $projectUser->payment_type === 'flat';
            if ($isFlat) {
                // Para pagos fijos, el monto es directo
                $flatRate = round($projectUser->flat_rate, 2);
                $invoices->push([
                    'user'               => $w,
                    'daily'              => [],
                    'subtotal'           => $flatRate,
                    'auto_adjustment'    => 0,
                    'manual_adjustment'  => 0.00,
                    'activity_index'     => null,
                    'period'             => $cutoffDate->format('F Y'),
                    'url'                => route('projects.show', $projectId),
                    'payment_type'       => 'flat',
                    'flat_rate'          => $flatRate,
                    'expected_salary'    => $flatRate,
                    'estimated_total'    => $flatRate // Para pagos flat, el total estimado es igual al flat rate
                ]);
                continue;
            }

            // Para pagos por hora
            $timings = Timming::where('project_id', $projectId)
                ->where('user_id', $w->id)
                ->whereBetween('from_timestamp', [$startTs, $endTs])
                ->get();

            // Agrupar por día
            $grouped = $timings->groupBy(fn($t) =>
                Carbon::createFromTimestamp($t->from_timestamp)
                    ->format('Y-m-d')
            );

            $rate    = $projectUser->hourly_rate;
            $daily   = [];
            $subtotal = 0;

            foreach ($grouped as $date => $group) {
                $hours  = round($group->count() * 10 / 60, 2);
                $amount = round($hours * $rate, 2);
                $daily[] = compact('date', 'hours', 'rate', 'amount');
                $subtotal += $amount;
            }

            // Activity index solo para pagos por hora
            $activityIndex = $timings->avg('activity_level') * 10;

            // Ajuste automático basado en activity index
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

            $subtotal = round($subtotal, 2);
            $estimatedTotal = round($subtotal + $auto, 2); // Calculamos el total estimado incluyendo el ajuste automático

            $invoices->push([
                'user'               => $w,
                'daily'              => $daily,
                'subtotal'           => $subtotal,
                'auto_adjustment'    => $auto,
                'manual_adjustment'  => 0.00,
                'activity_index'     => round($activityIndex, 2),
                'period'             => $cutoffDate->format('F Y'),
                'url'                => route('projects.show', $projectId),
                'payment_type'       => 'hourly',
                'hourly_rate'        => $rate,
                'estimated_total'    => $estimatedTotal
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
        $rawInvoices = $this->getProjectInvoices($projectId, $cutoffDate);

        // Mapeo para garantizar que existan todas las claves
        $prepared = $rawInvoices->map(function(array $inv) use ($projectId, $manuals) {
            $userId = $inv['user']->id;

            $subtotal = $inv['subtotal'] ?? 0;
            $auto     = $inv['auto_adjustment'] ?? 0;
            $manual   = round($manuals[$userId] ?? 0, 2);
            $total    = round($subtotal + $auto + $manual, 2);

            return array_merge($inv, [
                'project_id'        => $projectId,
                'subtotal'          => $subtotal,
                'auto_adjustment'   => $auto,
                'manual_adjustment' => $manual,
                'total'             => $total,
                'daily'             => $inv['daily'] ?? [],
            ]);
        });

        // En local solo enviamos el primero para tu demo
        if (app()->environment('local') && $prepared->isNotEmpty()) {
            $inv = $prepared[2];
            Mail::to('christianvondrak99@gmail.com')->send(new InvoiceMail($inv));

            $phone = "+584126054663";
            $name  = $inv['user']->first_name.' '.$inv['user']->last_name;
            $date  = now()->format('Y-m-d');                // o el periodo que quieras
            $amt   = $inv['total'];

            $this->whatsapp->sendAutoPayReminder($phone, $name, $date, $amt);

            return;
        }

        // En producción, uno por usuario:
//        foreach ($prepared as $inv) {
//            Mail::to($inv['user']->email)->send(new InvoiceMail($inv));
//        }
    }
}
