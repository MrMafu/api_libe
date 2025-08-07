<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Temp\FineController;

class GenerateOverdueFines extends Command
{
    protected $signature = 'fines:generate';
    protected $description = 'Generate overdue fines for late book returns';

    public function handle()
    {
        app(FineController::class)->checkOverdueFines();

        $this->info('Fines generation completed.');
    }
}
