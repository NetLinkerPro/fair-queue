<?php

namespace NetLinker\FairQueue\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;
use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;

class HorizonConfigurationBuild
{

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     * @throws FairQueueException
     */
    public function handle($event)
    {
        $horizon = FairQueue::getHorizon();

        Config::set('horizon.use', config('fair-queue.horizon_use'));
        Config::set('horizon.middleware', config('fair-queue.middleware'));
        Config::set('horizon.trim.recent', $horizon->trim_recent);
        Config::set('horizon.trim.recent_failed', $horizon->trim_recent_failed);
        Config::set('horizon.trim.failed', $horizon->trim_failed);
        Config::set('horizon.trim.monitored', $horizon->trim_monitored);
        Config::set('horizon.memory_limit', $horizon->memory_limit);

        $environments = $this->buildEnvironments($horizon);

        Config::set('horizon.environments', $environments);
    }

    /**
     * Build environments
     *
     * @param $horizon
     * @return array
     */
    private function buildEnvironments($horizon)
    {
        $config = [];

        $groups = $this->getGroupSupervisors($horizon);

        foreach ($groups as $environment => $supervisors) {

            // Create key environment in
            $config[$environment] = (isset($config[$environment])) ? $config[$environment] : null;

            foreach ($supervisors as $supervisor) {

                $queue = $this->buildSupervisorQueue($supervisor);

                $configSupervisor = [
                    'connection' => $supervisor->connection,
                    'queue' => $queue,
                    'maxProcesses' => $supervisor->min_processes,
                    'minProcesses' => $supervisor->max_processes,
                    'balance' => $supervisor->balance,
                    'nice' => $supervisor->priority,
                    'sleep' => $supervisor->sleep,
                    'tries' => 1,
                ];

                Arr::set($config, $environment . '.' . $supervisor->uuid, $configSupervisor);

            }

        }

        return $config;
    }

    /**
     * Build supervisor queue
     *
     * @param $supervisor
     * @return mixed
     */
    private function buildSupervisorQueue($supervisor)
    {
        $queues = Queue::where('supervisor_uuid', $supervisor->uuid)->get(['queue']);
        return $queues->pluck('queue')->toArray();
    }

    /**
     * Get group supervisors
     * @param $horizon
     * @return mixed
     */
    private function getGroupSupervisors($horizon)
    {

        $queues = Queue::where('horizon_uuid', $horizon->uuid)->distinct('supervisor_uuid')->get(['supervisor_uuid']);
        $supervisorUuids = $queues->pluck('supervisor_uuid')->toArray();

        return Supervisor::whereIn('uuid', $supervisorUuids)->get()->groupBy('environment');
    }
}


