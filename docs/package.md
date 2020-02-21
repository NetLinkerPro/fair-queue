# Package

This documentation present use FairQueue in package which use `orchestra/testbench`.

## Install

### Composer

Add package to composer `require`
```text
"netlinker/fair-queue": "^1.0",
```

Add repositories composer for AWES.io packages and NetLinker FairQueue package.
```text
"repositories": [
  {
    "type": "composer",
    "url": "https://repo.pkgkit.com",
    "options": {
      "http": {
        "header": ["API-TOKEN: {{your-awes-io-api-token}}"]
      }
    }
  },
  {
    "name": "netlinker/fair-queue",
    "type": "vcs",
    "url": "git@github.com:NetLinkerPro/fair-queue.git"
  }
],
```

Run command `composer update`.

## Configuration

Configuration for package is different as normal laravel instance. 

If your package use test with FairQueue and
example `Artisan::call('queue:work', ['--queue' => 'default']);`, you must declare configuration for `horizon` and
`queue`. 

For normal instance Laravel, horizon and queue is automation set after command `php artisan horizon`
in console.

### Horizon

Add to configuration `orchestra/testbench` provider and facade of horizon. In `TestCase` class add to return array
`getPackageProviders` class `Laravel\Horizon\HorizonServiceProvider`. 

Next in method `getPackageAliases` add
to array facade `'Horizon' => 'Laravel\Horizon\Horizon'`.

For simulate horizon add horizon, supervisor and queue to database.
```php
$horizon = factory(Horizon::class)->create();
$supervisor =  factory(Supervisor::class)->create();
$queue = factory(Queue::class)->create([
    'horizon_uuid' => $horizon->uuid,
    'supervisor_uuid' => $supervisor->uuid,
    'queue' => 'lead_allegro_import_auctions',
]);
```

For use by FairQueue simulation horizon and supervisor set resolver in `HorizonManager` and `QueueConfiguration`.
```php
QueueConfiguration::$queuesResolver= function () use (&$queue) {
    return ['lead_allegro_import_auctions' => $queue];
};
HorizonManager::$horizonResolver = function () use (&$horizon) {
    return $horizon;
};
```

### Owner

FairQueue use owner for among others job statuses. Set owner class in configuration FairQueue.
```php
$app['config']->set('fair-queue.owner.model', Owner::class);
```

### Model

For use FairQueue you must set model in configuration file.
```php
$app['config']->set('fair-queue.models.user', User::class);
```

You can change default model in configuration file.
```php
$app['config']->set('fair-queue.default_model', 'owner');
```