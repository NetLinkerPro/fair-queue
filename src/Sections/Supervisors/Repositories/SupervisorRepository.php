<?php

namespace NetLinker\FairQueue\Sections\Supervisors\Repositories;

use AwesIO\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;
use NetLinker\FairQueue\Sections\Supervisors\Scopes\SupervisorScopes;
use NetLinker\FairQueue\Tests\Stubs\User;

class SupervisorRepository extends BaseRepository
{

    protected $searchable = [];

    public function entity()
    {
        return Supervisor::class;
    }

    public function scope($request)
    {
        // apply build-in scopes
        parent::scope($request);

        // apply custom scopes
        $this->entity = (new SupervisorScopes($request))->scope($this->entity);

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
