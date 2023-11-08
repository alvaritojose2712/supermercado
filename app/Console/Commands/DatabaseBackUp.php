<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use env;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';
  
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
  
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
        
        $filename = "backup-" . date('Y_m_d_h_i_s') . ".sql";
  
        $command = "c:\\xampp\mysql\bin\mysqldump -u " . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " -h " . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . storage_path() . "/app/backup/" . $filename;
  
        $returnVar = NULL;
        $output  = NULL;
  
        exec($command, $output, $returnVar);
    }
}
