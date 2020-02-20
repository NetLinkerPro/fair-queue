<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use NetLinker\FairQueue\Sections\Accounts\Repositories\AccountRepository;
use NetLinker\FairQueue\Sections\Accounts\Resources\Account;
use NetLinker\FairQueue\Sections\Applications\Repositories\ApplicationRepository;
use NetLinker\FairQueue\Sections\Applications\Resources\Application;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;

class JobStatus extends JsonResource
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
            'type' => $this->type,
            'job_id' => $this->job_id,
            'interrupt' => (bool) $this->job_id,
            'external_uuid'=> $this->external_uuid,
            'queue' => $this->queue,
            'progress_percentage' => $this->progress_percentage,
            'status' => $this->status,
            '__status' =>  __('fair-queue::job-statuses.' . $this->status),
            'started_at' => optional($this->started_at)->format('Y-m-d H:i:s'),
            'finished_at' => optional($this->finished_at)->format('Y-m-d H:i:s'),
            'input' => ($this->input) ? json_encode($this->input, JSON_UNESCAPED_UNICODE| JSON_PRETTY_PRINT) : null,
            'output' => ($this->output) ? json_encode($this->output, JSON_UNESCAPED_UNICODE| JSON_PRETTY_PRINT) : null,
            'error' => $this->error,
            'logs' => $this->logs,
            'name' => $this->name,
            'horizon' => (new HorizonRepository())->findWhere(['uuid' => $this->horizon_uuid])->first(),
        ];
    }
}