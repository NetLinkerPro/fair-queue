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
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Sections\JobStatuses\Traits\Ownerable;
use NetLinker\FairQueue\Sections\JobStatuses\Traits\Trackable;

class TestErrorStatusJob implements ShouldQueue
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

        if ($this->isCanceled()){
            $this->setOutput([
                'canceled' => true,
            ]);
            return;
        }

        $this->setProgressMax(100);

        dump('run job');
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

            throw new FairQueueException('Test error');
        }

        $this->setOutput([
            'test_out' => true,
        ]);

        $this->setProgressNow($this->progressMax);
    }
}