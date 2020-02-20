<?php


namespace NetLinker\FairQueue;


use NetLinker\FairQueue\Events\QueueBooting;
use NetLinker\FairQueue\Facades\FairQueue;

trait EventListenerQueue
{

    /** @var bool $queueStarted */
    protected $queueStarted = false;

    /**
     * Register event listener queue
     */
    private function registerEventListenerQueue()
    {

        if (!$this->queueStarted){
            $this->queueStarted = true;

            if (FairQueue::runningAsHorizonWork()){
                event(new QueueBooting());
            }
        }

    }
}