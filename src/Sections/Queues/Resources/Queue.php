<?php

namespace NetLinker\FairQueue\Sections\Queues\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;
use NetLinker\FairQueue\Sections\Horizons\Resources\Horizon;
use NetLinker\FairQueue\Sections\Supervisors\Repositories\SupervisorRepository;
use NetLinker\FairQueue\Sections\Supervisors\Resources\Supervisor;
use NetLinker\FairQueue\SystemWorkLogger;

class Queue extends JsonResource
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
        $lastWorkLoggedAt = SystemWorkLogger::getQueueLastWorkLoggedAt($this->uuid);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'queue' => $this->queue,
            'refresh_max_model_id'=> $this->refresh_max_model_id,
            'active' => $this->active,
            'horizon' =>Horizon::collection((new HorizonRepository())->findWhere(['uuid' => $this->horizon_uuid]))[0],
            'horizon_uuid' => $this->horizon_uuid,
            'supervisor' =>Supervisor::collection((new SupervisorRepository())->findWhere(['uuid' => $this->supervisor_uuid]))[0],
            'supervisor_uuid' => $this->supervisor_uuid,
            'last_work_logged_at' => optional($lastWorkLoggedAt)->diffForHumans(),
            'last_work_danger' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_danger'))),
            'last_work_warning' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_warning'))),
        ];
    }
}