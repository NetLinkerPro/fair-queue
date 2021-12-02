<?php

namespace NetLinker\FairQueue\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\HorizonManager;
use NetLinker\FairQueue\Queues\QueueConfiguration;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;
use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;

class QueueConfigurationInitializing
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     * @throws FairQueueException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle($event)
    {
        QueueConfiguration::init();
    }
}


