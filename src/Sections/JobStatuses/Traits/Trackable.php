<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Traits;

use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Log;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;
use NetLinker\FairQueue\Sections\JobStatuses\Repositories\JobStatusRepository;
use Throwable;

trait Trackable
{
    /** @var int $statusId */
    public $statusId;

    /** @var int $progressNow */
    public $progressNow = 0;

    /** @var int $progressMax */
    public $progressMax = 0;

    /** @var string $logs */
    public $logs = '';

    /** @var $externalUuid */
    public $externalUuid;

    /** @var $ownerUuid */
    public $ownerUuid;

    /**
     * Set ownerUuid
     *
     * @param $value
     */
    protected function setOwnerUuid($ownerUuid)
    {
        $this->ownerUuid = $ownerUuid;
    }

    /**
     * Set progress max
     *
     * @param $value
     */
    protected function setProgressMax($value)
    {
        $this->update(['progress_max' => $value]);
        $this->progressMax = $value;
    }

    /**
     * Set progress now
     *
     * @param $value
     * @param int $every
     */
    protected function setProgressNow($value, $every = 1)
    {
        if ($value % $every == 0 || $value == $this->progressMax) {
            $this->update(['progress_now' => $value]);
        }
        $this->progressNow = $value;
    }

    /**
     * Increment progress
     *
     * @param int $offset
     * @param int $every
     */
    protected function incrementProgress($offset = 1, $every = 1)
    {
        $value = $this->progressNow + $offset;
        $this->setProgressNow($value, $every);
    }

    /**
     * Set input
     *
     * @param array $value
     */
    protected function setInput(array $value)
    {
        $this->update(['input' => $value]);
    }

    /**
     * Set output
     *
     * @param array $value
     */
    protected function setOutput(array $value)
    {
        $this->update(['output' => $value]);
    }

    /**
     * Set external uuid
     *
     * @param string $externalUuid
     */
    protected function setExternalUuid(string $externalUuid)
    {
        $this->update(['external_uuid' => $externalUuid]);
        $this->externalUuid = $externalUuid;
    }

    /**
     * Add log
     *
     * @param $value
     */
    protected function addLog(string $log)
    {
        $this->logs = $this->logs . PHP_EOL . $log;
        $this->update(['logs' => $this->logs]);
    }

    /**
     * Is interrupt
     *
     * @return bool
     */
    protected function isInterrupt()
    {
        if (!$this->ownerUuid) {
            $jobStatus = (new JobStatusRepository())->scopeOwner()->findOrFail($this->statusId);
        } else {
            $jobStatus = JobStatus::where('owner_uuid', $this->ownerUuid)
                ->where('id', $this->statusId)
                ->firstOrFail();
        }
        return $jobStatus->interrupt;
    }

    /**
     * Update
     *
     * @param array $data
     * @return |null
     */
    protected function update(array $data)
    {
        if (!$this->ownerUuid) {
            return (new JobStatusRepository())->scopeOwner()->update($data, $this->statusId);
        } else {
            if (!$this->statusId){
                throw new FairQueueException("Not found status ID.");
            }
            if (!$this->ownerUuid){
                throw new FairQueueException("Not found owner UUID.");
            }
            try{
                $jobStatus = JobStatus::where('id', $this->statusId)
                    ->where('owner_uuid', $this->ownerUuid)
                    ->firstOrFail();
            } catch (Throwable $exception){
                Log::error('Failed job status ' . $exception->getMessage(), ['exception' =>$exception]);
                throw $exception;
            }

            return $jobStatus
                ->update($data);
        }

    }

    /**
     * Prepare job status
     *
     * @param array $data
     */
    protected function prepareStatus(array $data = [])
    {
        $data = array_merge(['type' => $this->getDisplayName()], $data);

        if (!$this->ownerUuid) {

            $jobStatus = (new JobStatusRepository())->scopeOwner()->create($data);
        } else {

            $data = array_merge([
                'type' => $this->getDisplayName(),
            ], $data);

            $jobStatus = JobStatus::create($data);

            $jobStatus->owner_uuid = $this->ownerUuid;
            $jobStatus->save();
        }


        $this->statusId = $jobStatus->id;

    }

    /**
     * Get display name
     *
     * @return string
     */
    protected function getDisplayName()
    {
        return method_exists($this, 'displayName') ? $this->displayName() : static::class;
    }

    /**
     * Get job status ID
     *
     * @return int
     * @throws FairQueueException
     */
    public function getJobStatusId()
    {
        if ($this->statusId == null) {
            throw new FairQueueException("Failed to get jobStatusId, have you called \$this->prepareStatus() in __construct() of Job?");
        }

        return $this->statusId;
    }

    /**
     * Save queue
     *
     * @param $queue
     */
    public function saveQueue($queue)
    {
        $this->update(['queue' => $queue]);
    }

    /**
     * Is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {

        if (!$this->ownerUuid) {
            $jobStatus = (new JobStatusRepository())->scopeOwner()->findOrFail($this->statusId);
        } else {
            $jobStatus = JobStatus::where('owner_uuid', $this->ownerUuid)
                ->where('id', $this->statusId)
                ->firstOrFail();
        }
        return $jobStatus->cancel;
    }

}
