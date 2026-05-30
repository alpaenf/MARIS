<?php

namespace App\Jobs;

use App\Services\MarineIngestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarineIngestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->queue = 'ingest';
    }

    public function handle(MarineIngestService $service): void
    {
        $service->ingestHourly();
    }
}
