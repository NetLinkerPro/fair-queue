<?php

namespace NetLinker\FairQueue\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getModelFromQueue(string|null $queue)
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
