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
use Illuminate\Support\Str;
use NetLinker\FairQueue\Sections\JobStatuses\Traits\Ownerable;
use NetLinker\FairQueue\Sections\JobStatuses\Traits\Trackable;

class TestStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable, Ownerable;

    /** @var array $params */
    public $params;

    /** @var int modelId */
    public $modelId;

    /** @var int $tries */
    public $tries = 1;

    /** @var int $timeout */
    public $timeout = 120;

    /**
     * Constructor
     *
     * @param $accountId
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->prepareStatus(['name' => 'Test']);
        $this->prepareAuthUserJob();
        $this->modelId = $this->getAuthOwnerId();
        $this->params = $params;
        $this->setInput([
            'params' => $this->params,
            'test_input' => true,
        ]);
        $this->setExternalUuid(Str::uuid());
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function handle()
    {
        $this->loginUserJob();
        $this->setProgressMax(100);

        for ($i = 1; $i <= 100; $i++) {

            usleep(1000*10);
            $this->addLog('Log ' . $i);
            $this->setProgressNow($i);

            if ($this->isInterrupt()){
                $this->setOutput([
                    'interrupted' => true,
                ]);
                return;
            }
        }

        $this->setOutput([
            'test_out' => true,
        ]);

        $this->update([
            'message' => 'OK',
            'progress_now' => $this->progressMax,
        ]);
    }
}