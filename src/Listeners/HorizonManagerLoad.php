<?php

namespace NetLinker\FairQueue\Listeners;

use NetLinker\FairQueue\HorizonManager;

class HorizonManagerLoad
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event)
    {
        app()->singleton(HorizonManager::class, function($app) {
            return new HorizonManager();
        });
    }
}
