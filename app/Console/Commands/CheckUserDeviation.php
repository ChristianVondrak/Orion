<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckUserDeviation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:user-deviation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check each user for hour deviations and send alerts';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alerts)
    {
        $alerts->checkUserHourDeviation();
        $this->info('Checked user hour deviations.');
    }
}
