<?php

namespace App\Console\Commands;

use App\Services\WorksnapsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncWorksnapsData extends Command
{
    protected $signature = 'worksnaps:sync {--days=1}';
    protected $description = 'Sincroniza los datos de Worksnaps';

    public function __construct(private WorksnapsService $worksnapsService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $days = $this->option('days');
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days);

        $this->info("Iniciando sincronización de Worksnaps desde {$startDate} hasta {$endDate}");

        try {
            $this->info('Sincronizando usuarios...');
            $this->worksnapsService->syncUsers();

            $this->info('Sincronizando proyectos...');
            $this->worksnapsService->syncProjects();

            $this->info('Sincronizando registros de tiempo...');
            $this->worksnapsService->syncTimeEntries($startDate, $endDate);

            $this->info('Sincronización completada exitosamente');
        } catch (\Exception $e) {
            $this->error('Error durante la sincronización: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 