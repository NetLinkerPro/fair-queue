<?php

namespace NetLinker\FairQueue\Listeners;

use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Facades\FairQueue;

class HorizonUuidCheck
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

        if (!$horizonUuid) {
            throw new FairQueueException('Not found horizon UUID in configuration');
        }

        $horizon = FairQueue::getHorizon();

        if (!$horizon) {
            throw new FairQueueException('Not found horizon in database for UUID: ' . $horizonUuid);
        }
    }
}
