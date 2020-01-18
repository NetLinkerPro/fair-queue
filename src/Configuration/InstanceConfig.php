<?php


namespace NetLinker\FairQueue\Configuration;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class InstanceConfig
{

    /**
     * Get instance config
     *
     * @param bool $reload
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function get($reload = false)
    {
        $config = config('fair-queue.instance_config');

        if ($config && !$reload){
            return $config;
        }

        if (File::exists(storage_path('instance/fair-queue.json'))){

            static::saveNewQueues();

            $config = json_decode(File::get(storage_path('instance/fair-queue.json')), JSON_UNESCAPED_UNICODE);
        }

        if (!$config) {
            $config = static::createFromDefault();
        }

        Config::set('fair-queue.instance_config', $config);
        return $config;
    }

    /**
     * Detect from redis cache change config
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function detect()
    {
        $instanceUuid = config('fair-queue.instance_uuid');

        $cacheKey = 'fair-queue.instances.update_config.' . $instanceUuid;
        $updateConfig = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        $config = static::get();

        if ($updateConfig) {

            foreach ($updateConfig as $key => $value){
                Arr::set($config, $key, $value);
            }

            static::save($config);
            Config::set('fair-queue.instance_config', $config);
            Cache::store(config('fair-queue.cache_store'))->forget($cacheKey);

        }

        $cacheKeyLastUpdatedAt = 'fair-queue.instances.last_updated_at.' . $instanceUuid;
        $lastUpdateAt = Cache::store(config('fair-queue.cache_store'))->get($cacheKeyLastUpdatedAt);

        if (!$lastUpdateAt){
            return;
        }

        $lastUpdateAt = Carbon::fromSerialized($lastUpdateAt);

        $configLastUpdatedAt = Arr::get($config, 'last_updated_at');

        if (!$configLastUpdatedAt){
            static::get(true);
            return;
        }

        $configLastUpdatedAt = Carbon::fromSerialized($configLastUpdatedAt);

        if ($lastUpdateAt->gt($configLastUpdatedAt)){
            static::get(true);
            return;
        }

    }


    /**
     * Update
     *
     * @param $instanceUuid
     * @param array $data Example ['queues.queue_name.user.active' => true]
     */
    public static function update($instanceUuid, $data)
    {
        $cacheKey = 'fair-queue.instances.update_config.' . $instanceUuid;
        $cacheKeyLastUpdatedAt = 'fair-queue.instances.last_updated_at.' . $instanceUuid;

        $lastUpdatedAt = now()->serialize();

        $data['last_updated_at'] = $lastUpdatedAt;

        Cache::store(config('fair-queue.cache_store'))->forever($cacheKey, $data);
        Cache::store(config('fair-queue.cache_store'))->forever($cacheKeyLastUpdatedAt, $lastUpdatedAt);
    }

    /**
     * Save new queues from default instance configuration in config fair queue file
     */
    public static function saveNewQueues()
    {
        $fileInstanceConfig = json_decode(File::get(storage_path('instance/fair-queue.json')), JSON_UNESCAPED_UNICODE);
        $defaultQueues = Config::get('fair-queue.default_instance_config.queues', []);

        $existQueues = Arr::get($fileInstanceConfig, 'queues', []);
        $existQueueKeys = array_keys($existQueues);

        $mustSave = false;

        foreach($defaultQueues as $queueName => $defaultQueue){

            if (!in_array($queueName, $existQueueKeys)){

                Arr::set($fileInstanceConfig, 'queues.' . $queueName, $defaultQueue);
                $mustSave = true;

            } else {

                $existQueue = Arr::get($existQueues, $queueName, []);
                $mustSave = static::setNewModels($queueName, $defaultQueue, $existQueue, $fileInstanceConfig);

            }

        }

        if ($mustSave){
            static::save($fileInstanceConfig);
        }

    }

    /**
     * Set new models from default instance configuration in config fair queue file
     *
     * @param $queueName
     * @param $defaultQueue
     * @param $existQueue
     * @param $fileInstanceConfig
     * @return bool New models set
     */
    public static function setNewModels($queueName, $defaultQueue, $existQueue, &$fileInstanceConfig):bool
    {

        $existModels = array_keys($existQueue);

        $newModel = false;

        foreach ($defaultQueue as $modelName => $defaultModel){

            if (!in_array($modelName, $existModels)){

                Arr::set($fileInstanceConfig, 'queues.' . $queueName . '.' . $modelName , $defaultModel);
                $newModel= true;

            }

        }

        return $newModel;
    }

    /**
     * Create from default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    private static function createFromDefault()
    {
        $defaultConfig = config('fair-queue.default_instance_config');
        static::save($defaultConfig);
        return $defaultConfig;
    }

    /**
     * Save config
     *
     * @param $config
     */
    private static function save($config){

        if (!File::exists(storage_path('instance'))){

            File::makeDirectory(storage_path('instance'));
        }

        File::put(storage_path('instance/fair-queue.json'), json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

}