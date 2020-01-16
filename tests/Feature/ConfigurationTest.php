<?php


namespace NetLinker\FairQueue\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use NetLinker\FairQueue\Configuration\InstanceConfig;
use NetLinker\FairQueue\Tests\TestCase;


class ConfigurationTest extends TestCase
{

    public function test_generate_json_file_with_config()
    {
        $this->assertFileNotExists(storage_path('instance/fair-queue.json'));

        $systemConfig = InstanceConfig::get();
        $fileConfig = json_decode(File::get(storage_path('instance/fair-queue.json')), JSON_UNESCAPED_UNICODE);

        $systemConfigString = json_encode($systemConfig, JSON_UNESCAPED_UNICODE);
        $fileConfigString = json_encode($fileConfig, JSON_UNESCAPED_UNICODE);

        $this->assertEquals($systemConfigString, $fileConfigString);
    }

    public function test_get_config_from_system_after_load_from_file()
    {
        $config = InstanceConfig::get();

        Config::set('fair-queue.default_instance_config.active', false);
        $config['active'] = false;

        File::put(storage_path('instance/fair-queue.json'), json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $systemConfig = InstanceConfig::get();
        $this->assertTrue($systemConfig['active']);
    }

    public function test_remote_update_config(){

        Artisan::call('cache:clear');
        Redis::command('flushdb');

        $uuid = Str::uuid();

        Config::set('fair-queue.instance_uuid', $uuid);

        InstanceConfig::update($uuid, ['queues.default.user.allow_ids'=> [1,2,3]]);

        InstanceConfig::detect();

        $config = InstanceConfig::get();

        $this->assertEquals(3, sizeof(Arr::get($config, 'queues.default.user.allow_ids')));


    }

    public function test_save_new_queue_to_json_file(){

        InstanceConfig::get();

        Config::set('fair-queue.default_instance_config.queues.send_email', [
            'user' => [
                'active' => true
            ]
        ]);

        Config::set('fair-queue.instance_config');

        InstanceConfig::get();

        $fileConfig = json_decode(File::get(storage_path('instance/fair-queue.json')), JSON_UNESCAPED_UNICODE);

        $this->assertTrue(Arr::get($fileConfig, 'queues.send_email.user.active'));


    }

}