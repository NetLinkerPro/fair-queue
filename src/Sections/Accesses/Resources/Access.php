<?php

namespace NetLinker\FairQueue\Sections\Accesses\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use NetLinker\FairQueue\Sections\Queues\Repositories\QueueRepository;
use NetLinker\FairQueue\Sections\Queues\Resources\Queue;

class Access extends JsonResource
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
            'queue_uuid'=> $this->queue_uuid,
            'queue' => Queue::collection((new QueueRepository())->findWhere(['uuid' =>$this->queue_uuid ]))[0],
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'object_uuid' => $this->object_uuid,
            'active' => $this->active,
        ];
    }
}