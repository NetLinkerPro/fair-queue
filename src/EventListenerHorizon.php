<?php


namespace NetLinker\FairQueue;


use NetLinker\FairQueue\Events\HorizonBooting;
use NetLinker\FairQueue\Facades\FairQueue;

trait EventListenerHorizon
{

    /**
     * Register event listener horizon
     */
    private function registerEventListenerHorizon()
    {
        if (FairQueue::runningAsHorizon()){
            event(new HorizonBooting());
        }
    }
}