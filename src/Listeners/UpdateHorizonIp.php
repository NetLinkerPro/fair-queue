<?php

namespace NetLinker\FairQueue\Listeners;

use Illuminate\Support\Facades\Request;
use NetLinker\FairQueue\FairQueue;

class UpdateHorizonIp
{

    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        $horizon = (new FairQueue())->getHorizon();
        $horizon->ip = Request::ip();
        $horizon->save();
    }
}
