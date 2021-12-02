<?php

namespace NetLinker\FairQueue\Sections\Horizons\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use NetLinker\FairQueue\SystemWorkLogger;


class Horizon extends JsonResource
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
        $lastWorkLoggedAt = SystemWorkLogger::getHorizonLastWorkLoggedAt($this->uuid);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'memory_limit' => (int) $this->memory_limit,
            'trim_recent' => (int) $this->trim_recent,
            'trim_recent_failed'=> (int)$this->trim_recent_failed,
            'trim_failed' =>(int) $this->trim_failed,
            'trim_monitored' => (int)$this->trim_monitored,
            'active' => (bool)$this->active,
            'ip' => $this->ip,
            'launched_at' => optional($this->launched_at)->diffForHumans(),
            'last_work_logged_at' => optional($lastWorkLoggedAt)->diffForHumans(),
            'last_work_danger' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_danger'))),
            'last_work_warning' => optional($lastWorkLoggedAt)->lt(Carbon::now()->subSeconds(config('fair-queue.system_work_warning'))),
        ];
    }
}
