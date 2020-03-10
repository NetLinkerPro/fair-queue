<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Repositories;

use AwesIO\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Auth;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;
use NetLinker\FairQueue\Sections\JobStatuses\Scopes\JobStatusScopes;

class JobStatusRepository extends BaseRepository
{

    protected $searchable = [
        'name' => 'like',
        'status' => 'like',
        'external_uuid',
        'type' => 'like',
        'job_id',
        'id',
    ];

    public function entity()
    {
        return JobStatus::class;
    }

    public function scope($request)
    {
        // apply build-in scopes
        parent::scope($request);

        // apply custom scopes
        $this->entity = (new JobStatusScopes($request))->scope($this->entity);

        return $this;
    }

    public function scopeOwner()
    {
        $fieldUuid = config('fair-queue.owner.field_auth_user_owner_uuid');
        $ownerUuid = Auth::user()->$fieldUuid;

        $this->entity = $this->entity->where('owner_uuid', $ownerUuid);

        return $this;
    }

    /**
     * Delete a record by id.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $results = $this->entity->where('id', $id)->delete();

        $this->reset();

        return $results;
    }


    /**
     * Interrupt
     *
     * @param $id
     * @return int
     */
    public function interrupt($id)
    {
        return $this->scopeOwner()->update([
            'interrupt' => true,
        ], $id);
    }

    /**
     * Count executing by horizon
     *
     * @param $uuid
     * @return mixed
     */
    public function countExecutingByHorizon($uuid)
    {
        return $this->findWhere([
            'horizon_uuid' => $uuid,
            'status' => JobStatus::STATUS_EXECUTING,
        ])->count();
    }

    /**
     * Cancel
     *
     * @param $id
     * @return int
     */
    public function cancel($id)
    {
        return $this->scopeOwner()->update([
            'cancel' => true,
            'status' => JobStatus::STATUS_CANCELED,
        ], $id);
    }

}
