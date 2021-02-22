<?php

namespace App\Console\Commands;

use App\Services\AddUSDColumnService;
use Illuminate\Console\Command;

class AddUSDToFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:usd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding USD to CSV file';

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
        $addColumnService = new AddUSDColumnService();
        $addColumnService->addUSDColumn();
    }
}
