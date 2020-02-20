<?php

namespace NetLinker\FairQueue\Listeners;

use NetLinker\FairQueue\Queues\QueueConfiguration;

class QueueConfigurationLoad
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event)
    {
        app()->singleton(QueueConfiguration::class, function($app) {
            return new QueueConfiguration();
        });
    }
}
