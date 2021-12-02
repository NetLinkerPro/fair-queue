<?php

namespace NetLinker\FairQueue\Listeners;

use NetLinker\FairQueue\Facades\FairQueue;

class HorizonLaunchedAtSet
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event)
    {
        $horizon = FairQueue::getHorizon();
        $horizon->launched_at = now();
        $horizon->save();
    }
}
