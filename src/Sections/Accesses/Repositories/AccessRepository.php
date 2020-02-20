<?php

namespace NetLinker\FairQueue\Sections\Accesses\Repositories;

use AwesIO\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\FairQueue;
use NetLinker\FairQueue\Sections\Accesses\Models\Access;
use NetLinker\FairQueue\Sections\Accesses\Scopes\AccessScopes;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;

class AccessRepository extends BaseRepository
{

    protected $searchable = [];

    public function entity()
    {
        return Access::class;
    }

    public function scope($request)
    {
        // apply build-in scopes
        parent::scope($request);

        // apply custom scopes
        $this->entity = (new AccessScopes($request))->scope($this->entity);

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

    /**
     * Get queue objects
     *
     * @param $queueUuid
     * @param $q
     * @return array
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getQueueObjects($queueUuid, $q)
    {
        $queue = Queue::getByUuid($queueUuid)->queue;

        $class = (new FairQueue())->getClassModelByQueue($queue);

        $query = $this->buildQueryCollectionsBySearch($class, $q);

        $entities = $query->get();

        $objects = collect();

        /** @var Model $entity */
        foreach ($entities as $entity) {

            if (!$entity->isFillable('uuid')) {
                throw new FairQueueException(__('fair-queue::accesses.object_has_not_field_uuid'));
            }

            $name = $this->buildQueueObjectName($entity);

            $objects->push([
                'name' => $name,
                'id' => $entity->getKey(),
                'uuid' => $entity->uuid,
            ]);


        }

        return $objects;
    }


    /**
     * @param Model $class
     * @param $q
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function buildQueryCollectionsBySearch($class, $q)
    {
        /** @var Model $entity */
        $entity = app()->make($class);

        $query = $class::query();

        if (!$q) {
            $query->take(300);
        } else {

            $query->where($entity->getKeyName(), 'like', '%'.$q.'%');

            if ($entity->isFillable('uuid')) {
                $query->orWhere('uuid', 'like', '%'.$q.'%');
            }

            if ($entity->isFillable('name')) {
                $query->orWhere('name', 'like', '%'.$q.'%');
            }

            if ($entity->isFillable('description')) {
                $query->orWhere('description', 'like', '%'.$q.'%');
            }

            if ($entity->timestamps) {
                $query->orWhere('created_at', 'like', '%'.$q.'%');
                $query->orWhere('updated_at', 'like', '%'.$q.'%');
            }

        }


        return $query->orderBy($entity->getKeyName(), 'desc');

    }

    /**
     * Build entity name
     *
     * @param Model $entity
     * @return string
     */
    private function buildQueueObjectName(Model $entity)
    {

        $name = $entity->getKey() . ':';

        if ($entity->name) {
            $name .= ' ' . $entity->name;
        }

        if ($entity->owner_uuid) {
            $name .= ' ' . $entity->owner_uuid;
        } else if ($entity->uuid) {
            $name .= ' ' . $entity->uuid;
        }
        return $name;
    }



}
