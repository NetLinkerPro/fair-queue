<?php

namespace NetLinker\FairQueue\Listeners;

use Laravel\Horizon\MasterSupervisor;
use NetLinker\FairQueue\Facades\FairQueue;

class HorizonNameBuild
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
        $name = $horizon->uuid;

        MasterSupervisor::$nameResolver = function() use(&$name){
            return $name;
        };
    }
}
