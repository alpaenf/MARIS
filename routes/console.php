<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\BmkgIngestService;
use App\Services\MarineIngestService;
use App\Jobs\BmkgIngestJob;
use App\Jobs\MarineIngestJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bmkg:ingest-hourly', function (BmkgIngestService $service) {
    $service->ingestHourly();
})->purpose('Ingest BMKG hourly forecast data');

Artisan::command('marine:ingest-hourly', function (MarineIngestService $service) {
    $service->ingestHourly();
})->purpose('Ingest marine hourly data');

Schedule::job(new BmkgIngestJob())
    ->hourly()
    ->withoutOverlapping();

Schedule::job(new MarineIngestJob())
    ->hourly()
    ->withoutOverlapping();
