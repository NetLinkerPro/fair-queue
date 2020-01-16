<?php


namespace NetLinker\FairQueue\Tests\Mocks;


use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $modelId = 0;

    public $handleSleep = 0;

    public function handle(): void
    {
      //  usleep(1000 * $this->handleSleep);
        Cache::increment('test_job');
        dump('Handle test job ' . $this->modelId);
    }


    public function failed(Exception $exception)
    {
        dump($exception->getMessage() . PHP_EOL .$exception->getTraceAsString());
    }
}