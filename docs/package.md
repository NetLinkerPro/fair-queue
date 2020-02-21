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