<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Default models with identifier
     |
     | Model must contains ID column. Model selected from below `models` array.
     |--------------------------------------------------------------------------
     */
    'default_model' => 'user',

    /*
     |--------------------------------------------------------------------------
     | Models with identifier
     |
     | Models must contains ID column
     |--------------------------------------------------------------------------
     */
    'models' => [
        'user' => 'App\User',
    ],

    /*
     |--------------------------------------------------------------------------
     | Unique identify instance laravel
     |--------------------------------------------------------------------------
     */
    'instance_uuid' => env('INSTANCE_UUID', ''),

    /*
     |--------------------------------------------------------------------------
     | Default config instance laravel
     |--------------------------------------------------------------------------
     */
    'default_instance_config' => [

        'active' => true,

        'queues' =>[

            'default' => [

                'user' => [ // model name

                    'active' => false,

                    'refresh_max_id' => 60, // seconds

                    'allow_ids' => [],

                    'exclude_ids' => [],

                ]
            ]
        ]
    ],

    /*
     |--------------------------------------------------------------------------
     | Cache store
     |--------------------------------------------------------------------------
     */
    'cache_store' => 'redis',

];