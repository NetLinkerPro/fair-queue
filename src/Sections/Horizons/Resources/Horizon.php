<?php

namespace NetLinker\FairQueue\Sections\Horizons\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Horizon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
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
        ];
    }
}
