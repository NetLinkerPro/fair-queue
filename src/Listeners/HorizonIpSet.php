<?php

namespace NetLinker\FairQueue\Listeners;

use Illuminate\Support\Facades\Request;
use NetLinker\FairQueue\Facades\FairQueue;

class HorizonIpSet
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
        $horizon->ip = Request::ip();
        $horizon->save();
    }
}
