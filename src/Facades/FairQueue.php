<?php

namespace NetLinker\FairQueue\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getModelFromQueue(string|null $queue)
 * @method static runningAsHorizon()
 * @method static getHorizon()
 * @method static getSupervisor()
 * @method static getQueues()
 * @method static runningAsHorizonWork()
 * @method static runningAsHorizonSupervisor()
 * @method static getClassModelByQueue($queue)
 *
 * @see \NetLinker\FairQueue\FairQueue
 */
class FairQueue extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fair-queue';
    }
}
