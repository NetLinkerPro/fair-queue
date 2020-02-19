<?php

namespace NetLinker\FairQueue\Sections\Queues\Repositories;

use AwesIO\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use NetLinker\FairQueue\FairQueue;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;
use NetLinker\FairQueue\Sections\Queues\Scopes\QueueScopes;
use NetLinker\FairQueue\Tests\Stubs\User;

class QueueRepository extends BaseRepository
{

    protected $searchable = [];

    public function entity()
    {
        return Queue::class;
    }

    public function scope($request)
    {
        // apply build-in scopes
        parent::scope($request);

        // apply custom scopes
        $this->entity = (new QueueScopes($request))->scope($this->entity);

        return $this;
    }

    /**
     * Delete a record by id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $results = $this->entity->where('id', $id)->delete();

        $this->reset();

        return $results;
    }


}
