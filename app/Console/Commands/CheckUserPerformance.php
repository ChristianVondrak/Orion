<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckUserPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:user-performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detects and alerts on performances <75% or >97% per professional';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alerts)
    {
        $alerts->checkUserPerformance();
        $this->info('Checked user performance alerts.');
    }
}
