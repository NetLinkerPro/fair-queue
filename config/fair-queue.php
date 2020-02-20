<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Unique identify horizon
     |--------------------------------------------------------------------------
     */
    'horizon_uuid' => env('HORIZON_UUID', null),

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

        'horizons' => 'NetLinker\FairQueue\Sections\Horizons\Controllers\HorizonController',

        'queues' => 'NetLinker\FairQueue\Sections\Queues\Controllers\QueueController',

        'supervisors' => 'NetLinker\FairQueue\Sections\Supervisors\Controllers\SupervisorController',

        'accesses' => 'NetLinker\FairQueue\Sections\Accesses\Controllers\AccessController',
    ],

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'horizon_use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Fair queue Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Laravel will store
    | the jobs.
    |
    */

    'connection' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Default queue
    |--------------------------------------------------------------------------
    |
    | This is the name of the queue where will push jobs without name queue.
    |
    */

    'default_queue' => 'test_job',

    /*
    |--------------------------------------------------------------------------
    | Retry after
    |--------------------------------------------------------------------------
    |
    | Value to the maximum number of seconds your jobs should reasonably
    | take to complete processing
    |
    */

    'retry_after' => 172800,

    /*
    |--------------------------------------------------------------------------
    | Block for
    |--------------------------------------------------------------------------
    |
    | Configuration option to specify how long the driver should wait for a
    | job to become available before iterating through the worker loop and
    | re-polling the Redis database.
    |
    */

    'block_for' => null,


    /*
    |--------------------------------------------------------------------------
    | System work danger
    |--------------------------------------------------------------------------
    |
    | Seconds for show status danger for work system in front manage.
    |
    */
    'system_work_danger' => 3600,

    /*
    |--------------------------------------------------------------------------
    | System work warning
    |--------------------------------------------------------------------------
    |
    | Seconds for show status warning for work system in front manage.
    |
    */
    'system_work_warning' => 60,
];