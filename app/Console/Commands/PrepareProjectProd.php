<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use App\Models\Payment\Plan;
use App\Models\Proxy;


class PrepareProjectProd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare project DB for testing on prod';

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
        print('Start' . PHP_EOL);

        print('Migrations' . PHP_EOL);

        print('Custom data' . PHP_EOL);
        
        print('DB seed' . PHP_EOL);

        print('Complete');
        
        return 0;
    }
}
