<?php

namespace NetLinker\FairQueue\Listeners;

use Illuminate\Support\Facades\Request;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\FairQueue;

class CheckHorizonUuid
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     * @throws FairQueueException
     */
    public function handle($event)
    {
        $horizonUuid = config('fair-queue.horizon_uuid');

        if (!$horizonUuid){
            throw new FairQueueException('Not found horizon UUID in configuration');
        }
    }
}
