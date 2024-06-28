<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropAllTablesExcept extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:drop-all-tables-except';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Drop all tables except specified ones';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $exceptTables = [
      'areas',
      'area_product',
      'products',
      'categories',
      'units',
      'users',
      'sessions',
      'cache',
      'cache_locks',
      'failed_jobs',
      'job_batches',
      'jobs',
      'password_reset_tokens',
      'migrations'
    ];

    // Disable foreign key checks to avoid issues with foreign key constraints
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    // Get all table names in the database
    $tables = DB::select('SHOW TABLES');
    $dbName = 'Tables_in_' . env('DB_DATABASE');

    foreach ($tables as $table) {
      $tableName = $table->$dbName;
      if (!in_array($tableName, $exceptTables)) {
        // Truncate table if it's not in the exceptions list
        DB::table($tableName)->truncate();
        $this->info("Truncated table: $tableName");
      }
    }

    // Enable foreign key checks back
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $this->info('All tables except specified ones have been truncated.');
  }
}
