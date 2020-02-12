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

                    'active' => true,

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

    /*
    |--------------------------------------------------------------------------
    | Owner
    |--------------------------------------------------------------------------
    |
    | Owner class for automation add owner to model.
    |
    */

    'owner' => [
        'model' => 'NetLinker\FairQueue\Tests\Stubs\Owner',
        'field_auth_user_owner_uuid' => 'owner_uuid'
    ],


    /*
   |--------------------------------------------------------------------------
   | Domain
   |--------------------------------------------------------------------------
   |
   | Route domain for module FairQueue. If null, domain will be
   | taken from `app.url` config.
   |
   */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Prefix
    |--------------------------------------------------------------------------
    |
    | Route prefix for module.
    |
    */

    'prefix' => 'fair-queue',


    /*
    |--------------------------------------------------------------------------
    | Web middleware
    |--------------------------------------------------------------------------
    |
    | Middleware for routes module FairQueue. Value is array.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Controllers
    |--------------------------------------------------------------------------
    |
    | Namespaces for controllers.
    |
    */

    'controllers' => [

        'assets' => 'NetLinker\FairQueue\Sections\Assets\Controllers\AssetController',

        'dashboard' => 'NetLinker\FairQueue\Sections\Dashboard\Controllers\DashboardController',

        'job_statuses' => 'NetLinker\FairQueue\Sections\JobStatuses\Controllers\JobStatusController',

    ],
];