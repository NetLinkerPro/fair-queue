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
        \NetLinker\FairQueue\Events\HorizonBooting::class => [
            \NetLinker\FairQueue\Listeners\HorizonUuidCheck::class,
            \NetLinker\FairQueue\Listeners\HorizonNameBuild::class,
            \NetLinker\FairQueue\Listeners\HorizonIpSet::class,
            \NetLinker\FairQueue\Listeners\HorizonLaunchedAtSet::class,
            \NetLinker\FairQueue\Listeners\HorizonConfigurationBuild::class,
        ],
        \NetLinker\FairQueue\Events\SupervisorBooting::class => [

        ],
        \NetLinker\FairQueue\Events\QueueBooting::class => [
            \NetLinker\FairQueue\Listeners\QueueConfigurationLoad::class,
            \NetLinker\FairQueue\Listeners\HorizonManagerLoad::class,
        ],
        \NetLinker\FairQueue\Events\QueueFairJobFinding::class => [
            \NetLinker\FairQueue\Listeners\HorizonManagerListening::class,
            \NetLinker\FairQueue\Listeners\QueueConfigurationListening::class,
            \NetLinker\FairQueue\Listeners\SystemWorkLogging::class,
        ],
        \NetLinker\FairQueue\Events\QueuePopping::class => [
            \NetLinker\FairQueue\Listeners\HorizonManagerInitializing::class,
            \NetLinker\FairQueue\Listeners\QueueConfigurationInitializing::class,
        ],
    ];
}
