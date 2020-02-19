<?php

namespace NetLinker\FairQueue;

trait EventMap
{

    /**
     * All of the Horizon event / listener mappings.
     *
     * @var array
     */
    protected $events = [
        \NetLinker\FairQueue\Events\WorkerStarting::class => [
            \NetLinker\FairQueue\Listeners\CheckHorizonUuid::class,
           \NetLinker\FairQueue\Listeners\UpdateHorizonIp::class,
        ],
    ];
}
