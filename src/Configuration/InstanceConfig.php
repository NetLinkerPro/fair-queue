<?php


namespace Netlinker\FairQueue\Configuration;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class InstanceConfig
{

    /**
     * Get instance config
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function get()
    {
        $config = config('fair-queue.instance_config');

        if ($config){
            return $config;
        }

        if (File::exists(storage_path('instance/fair-queue.json'))){
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
     */
    public static function detect()
    {
        $instanceUuid = config('fair-queue.instance_uuid');

        $cacheKey = 'fair-queue.instances.update_config.' . $instanceUuid;
        $updateConfig = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        if ($updateConfig) {

            $config = static::get();

            foreach ($updateConfig as $key => $value){
                Arr::set($config, $key, $value);
            }

            static::save($config);
            Config::set('fair-queue.instance_config', $config);
            Cache::store(config('fair-queue.cache_store'))->forget($cacheKey);
        }

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

    /**
     * Update
     *
     * @param $instanceUuid
     * @param array $data Example ['queues.queue_name.user.active' => true]
     */
    public static function update($instanceUuid, $data)
    {
        $cacheKey = 'fair-queue.instances.update_config.' . $instanceUuid;

        Cache::store(config('fair-queue.cache_store'))->forever($cacheKey, $data);
    }
}