<?php


namespace NetLinker\FairQueue;


use NetLinker\FairQueue\Events\SupervisorBooting;
use NetLinker\FairQueue\Facades\FairQueue;

trait EventListenerSupervisor
{

    /** @var bool $supervisorStarted */
    protected $supervisorStarted = false;

    /**
     * Register event listener supervisor
     */
    private function registerEventListenerSupervisor()
    {

        if (!$this->supervisorStarted){
            $this->supervisorStarted = true;

            $supervisor = FairQueue::getSupervisor();

            if ($supervisor){
                event(new SupervisorBooting($supervisor));
            }

        }

    }
}