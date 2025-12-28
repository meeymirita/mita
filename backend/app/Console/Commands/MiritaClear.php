<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MiritaClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mirita:my-optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сразу всё что есть (cache,view,route,config,optimize,config,route,optimize)';

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

        return Command::SUCCESS;
    }
}
