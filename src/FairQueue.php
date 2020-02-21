<?php

namespace NetLinker\FairQueue;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;
use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;
use NetLinker\FairQueue\Sections\Supervisors\Repositories\SupervisorRepository;

class FairQueue
{

    /**
     * Get model from queue
     *
     * @param $queue
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getModelFromQueue($queue = null){

        $queue = $queue ?? config('fair-queue.default_queue');

        $queueParts = explode(':', $queue);

        // get model from queue
        $model = (sizeof($queueParts)> 1) ? $queueParts[1] : null;

        // get default model
        if (!$model){
            $model =  config('fair-queue.default_model');
        }

        return $model;
    }

    /**
     * Get class model by queue
     *
     * @param $queue
     * @return \Illuminate\Config\Repository|mixed
     * @throws FairQueueException
     */
    public function getClassModelByQueue($queue){
        $model = $this->getModelFromQueue($queue);
        return $this->getClassModel($model);
    }
    /**
     * Get class model
     *
     * @param $model
     * @return \Illuminate\Config\Repository|mixed
     * @throws FairQueueException
     */
    public function getClassModel($model){
        $class = config('fair-queue.models.' . $model);

        if (!$class){
            throw new FairQueueException(__('fair-queue::queues.exception_not_found_model'));
        }

        return $class;
    }

    /**
     * Get horizon
     *
     * @return Horizon|null
     */
    public function getHorizon(){
        $horizonUuid = config('fair-queue.horizon_uuid');
        return (new HorizonRepository())->findWhere(['uuid' => $horizonUuid])->first();
    }

    /**
     * Get supervisor
     *
     * @return Supervisor|null
     */
    public function getSupervisor(){

        if (!$this->runningAsHorizonSupervisor()){
            return null;
        }

        $args = $_SERVER['argv'];

        if (sizeof($args) < 3){
            return null;
        }

        $argName = $args[2];

        if (!Str::contains($argName,':')){
            return null;
        }

        $supervisorUuid = explode(':', $argName, 2)[1];

        return (new SupervisorRepository())->findWhere(['uuid' => $supervisorUuid])->first();
    }

    /**
     * Get queues
     *
     * @return Collection
     */
    public function getQueues(){

        if (!$this->runningAsHorizonWork()){
            return collect();
        }

        $args = $_SERVER['argv'];

        $supervisorUuid = null;
        $queues = [];

        foreach ($args as $arg){

            if (Str::startsWith($arg,'--supervisor=')){

                if (Str::contains($arg, ':')){

                    $data=explode(':', $arg);

                    $supervisorUuid = $data[sizeof($data) - 1];
                }
            }
            if (Str::startsWith($arg,'--queue=')){

                $queue=explode('queue=', $arg)[1];
                $queues = explode(',', $queue);
            }

        }

        if (!$supervisorUuid || !$queues){
            return collect();
        }

        return Queue::whereIn('queue', $queues)->where('supervisor_uuid', $supervisorUuid)->get();
    }

    /**
     * Running as horizon supervisor
     *
     * @return bool
     */
    public function runningAsHorizonSupervisor(){
       $args =  join(' ', $_SERVER['argv']);
       return Str::contains($args, 'artisan horizon:supervisor ');
    }

    /**
     * Running as horizon work
     *
     * @return bool
     */
    public function runningAsHorizonWork(){
        $args =  join(' ', $_SERVER['argv']);
        return Str::contains($args, 'artisan horizon:work ');
    }


    /**
     * Running as horizon
     *
     * @return bool
     */
    public function runningAsHorizon(){

        $args = join(' ', $_SERVER['argv']);

        return Str::contains($args, 'artisan horizon ') || Str::endsWith($args, 'artisan horizon');
    }

}
