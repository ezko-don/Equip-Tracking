<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetAndSeedDatabase extends Command
{
    protected $signature = 'db:reset-and-seed';
    protected $description = 'Reset and seed the database with fresh data';

    public function handle()
    {
        if (!app()->environment('local')) {
            $this->error('This command can only be run in the local environment.');
            return 1;
        }

        $this->info('Disabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = ['users', 'categories', 'equipment', 'tasks', 'bookings'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("Truncating {$table} table...");
                DB::table($table)->truncate();
            }
        }

        $this->info('Re-enabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Running database seeder...');
        $this->call('db:seed');

        $this->info('Database has been reset and seeded successfully!');
        return 0;
    }
} 