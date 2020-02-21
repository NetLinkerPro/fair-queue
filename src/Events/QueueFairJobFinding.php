<?php

namespace NetLinker\FairQueue\Events;

class QueueFairJobFinding
{

    /** @var string $queue */
    public $queue;

    /**
     * Constructor
     *
     * @param string $queue
     */
    public function __construct(string $queue)
    {
        $this->queue = $queue;
    }

}
