<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proxy\Proxy;

class TrialProxyExpirer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxies:trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check is trial expired. If exprired, remove from user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Proxy::where('is_trial', true)->get()->map(function($proxy) {
            if ($proxy->isExpired()) 
                $proxy->update([
                    'is_trial'              => false,
                    'trial_ends_at'         => null,
                    'user_id'               => null,
                    'is_action_required'    => true
                ]);
        });

        return 0;
    }
}
