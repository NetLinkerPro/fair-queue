<?php

namespace NetLinker\FairQueue\Events;

use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;

class SupervisorBooting
{

    /** @var Supervisor $supervisor */
    public $supervisor;

    /**
     * Constructor
     *
     * @param $supervisor
     */
    public function __construct($supervisor)
    {
        $this->supervisor = $supervisor;
    }

}
