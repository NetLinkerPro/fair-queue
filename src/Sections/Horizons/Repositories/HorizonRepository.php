<?php

namespace NetLinker\FairQueue\Sections\Horizons\Repositories;

use AwesIO\Repository\Eloquent\BaseRepository;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use NetLinker\FairQueue\Sections\Horizons\Scopes\HorizonScopes;

class HorizonRepository extends BaseRepository
{

    protected $searchable = [];

    public function entity()
    {
        return Horizon::class;
    }

    public function scope($request)
    {
        // apply build-in scopes
        parent::scope($request);

        // apply custom scopes
        $this->entity = (new HorizonScopes($request))->scope($this->entity);

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
     * Is active
     *
     * @param $id
     * @return mixed
     */
    public function isActive($id)
    {
        return $this->entity->where('id', $id)->first()->active;
    }

    /**
     * Get UUID
     *
     * @param $id
     * @return mixed
     */
    public function getUuid($id)
    {
        return $this->entity->where('id', $id)->first()->uuid;
    }


}
