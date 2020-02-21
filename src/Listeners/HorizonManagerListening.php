<?php

namespace NetLinker\FairQueue\Listeners;

use NetLinker\FairQueue\HorizonManager;
use NetLinker\FairQueue\Queues\QueueConfiguration;
use NetLinker\FairQueue\SystemWorkLogger;

class HorizonManagerListening
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle($event)
    {
        HorizonManager::listen();
    }
}
