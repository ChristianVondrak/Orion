<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckProjectDeviation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:project-deviation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check each project for hour deviations and send alerts';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $svc)
    {
        $svc->checkProjectHourDeviation();
        $this->info('Checked project hour deviations.');
    }
}
