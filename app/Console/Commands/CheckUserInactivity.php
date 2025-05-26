<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckUserInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:user-inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect contractors without activity for N days and send alerts';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alerts)
    {
        $alerts->checkUserInactivity();
        $this->info('Checked user inactivity alerts.');
    }
}
