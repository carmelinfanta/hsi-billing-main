<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendClickAlerts;
use App\Console\Commands\GenerateApiToken;
use App\Console\Commands\UpdatePendingInvoices;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {

//     $this->comment(Inspiring::quote());
    
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('clicks:weekly-alerts')->weekly()->mondays()->at('09:00')->onOneServer();

Schedule::command('clicks:send-alerts')->daily()->onOneServer();

Schedule::command('invoices:updatePendingInvoices')->hourly()->onOneServer();

Schedule::command('data:send-reminder')->daily()->onOneServer();

Schedule::command('plans:send-reminder')->daily()->onOneServer();