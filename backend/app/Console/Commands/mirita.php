<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mirita extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mirita';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // все клир
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('optimize:clear');


        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('optimize');

        $this->call('migrate:fresh');

        return Command::SUCCESS;
    }
}
