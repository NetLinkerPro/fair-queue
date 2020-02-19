<?php

namespace NetLinker\FairQueue\Sections\Supervisors\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Supervisor extends JsonResource
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
            'environment' => $this->environment,
            'connection'=> $this->connection,
            'balance' =>$this->balance,
            'min_processes' => $this->min_processes,
            'max_processes' => $this->max_processes,
            'priority' => $this->priority,
            'active' => $this->active,
        ];
    }
}
