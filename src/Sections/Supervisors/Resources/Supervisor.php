<?php

namespace NetLinker\FairQueue\Sections\Supervisors\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use NetLinker\FairQueue\SystemWorkLogger;

class Supervisor extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function toArray($request)
    {
        $lastWorkLoggedAt = SystemWorkLogger::getSupervisorLastWorkLoggedAt($this->uuid);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'environment' => $this->environment,
            'connection'=> $this->connection,
            'balance' =>$this->balance,
            'min_processes' => $this->min_processes,
            'max_processes' => $this->max_processes,
            'priority' => $this->priority,
            'active' => $this->active,
            'sleep' => $this->sleep,
            'last_work_logged_at' => optional($lastWorkLoggedAt)->diffForHumans(),
            'last_work_danger' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_danger'))),
            'last_work_warning' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_warning'))),
        ];
    }
}
