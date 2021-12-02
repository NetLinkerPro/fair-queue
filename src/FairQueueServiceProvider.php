<?php

namespace NetLinker\FairQueue;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use NetLinker\FairQueue\Connectors\FairQueueConnector;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;
use NetLinker\FairQueue\Sections\JobStatuses\Repositories\JobStatusRepository;
use \NetLinker\FairQueue\Sections\JobStatuses\BladeDirectives\JobStatuses as BladeDirectiveJobStatuses;

class FairQueueServiceProvider extends ServiceProvider
{

    use EventMap, EventListenerSupervisor, EventListenerHorizon, EventListenerQueue, BladeDirectiveJobStatuses;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();

        $this->bladeDirectiveJobStatusBoot();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fair-queue');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fair-queue');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->registerFairQueueConnector();

        $this->registerEventListenerJobStatuses();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {


            $this->registerEventListenerHorizon();

            $this->registerEventListenerSupervisor();

            $this->registerEventListenerQueue();

            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fair-queue.php', 'fair-queue');

        // Register the service the package provides.
        $this->app->singleton('fair-queue', function ($app) {
            return new FairQueue;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['fair-queue'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/fair-queue.php' => config_path('fair-queue.php'),
        ], 'config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/fair-queue'),
        ], 'views');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/fair-queue'),
        ], 'views');

        // Publishing the translation files.
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/fair-queue'),
        ], 'views');

        // Registering package commands.
        $this->commands([]);
    }

    /**
     * Register the Horizon job events.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $events = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }
    /**
     * Register in application fair queue connector
     */
    private function registerFairQueueConnector()
    {
        Config::set('queue.connections.fair-queue', [
            'driver' => 'fair-queue',
            'connection' => config('fair-queue.connection'),
            'queue' => config('fair-queue.default_queue'),
            'retry_after' => config('fair-queue.retry_after'),
            'block_for' => config('fair-queue.block_for'),
        ]);

        $this->app->resolving(QueueManager::class, function ($manager) {
            $manager->addConnector('fair-queue', function () {
                return new FairQueueConnector($this->app['redis']);
            });
        });
    }

    /**
     * Register event listener job statuses
     */
    private function registerEventListenerJobStatuses()
    {
        /** @var JobStatus $entityClass */
        $entityClass = app()->getAlias(JobStatus::class);

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($entityClass) {

            /** @var HorizonManager $horizonManager */
            $horizonManager = app()->make(HorizonManager::class);

            $this->updateJobStatus($event->job, [
                'status' => $entityClass::STATUS_EXECUTING,
                'job_id' => $event->job->getJobId(),
                'queue' => $event->job->getQueue(),
                'started_at' => Carbon::now(),
                'horizon_uuid' => $horizonManager->horizon->uuid,
            ]);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($entityClass) {

            $this->updateJobStatus($event->job, [
                'status' => $entityClass::STATUS_FINISHED,
                'finished_at' => Carbon::now()
            ]);
        });

        app(QueueManager::class)->failing(function (JobFailed $event) use ($entityClass) {
            $this->updateJobStatus($event->job, [
                'status' => $entityClass::STATUS_FAILED,
                'finished_at' => Carbon::now(),
                'error' => $event->exception->getMessage() . PHP_EOL . $event->exception->getTraceAsString(),
            ]);
        });

        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($entityClass) {
            $this->updateJobStatus($event->job, [
                'status' => $entityClass::STATUS_FAILED,
                'finished_at' => Carbon::now(),
                'error' => $event->exception->getMessage() . PHP_EOL . $event->exception->getTraceAsString(),
            ]);
        });
    }

    /**
     * Update job status
     *
     * @param Job $job
     * @param array $data
     * @return |null
     */
    private function updateJobStatus(Job $job, array $data)
    {
        try {
            $payload = $job->payload();
            $jobStatus = unserialize($payload['data']['command']);

            if (!is_callable([$jobStatus, 'getJobStatusId'])) {
                return null;
            }

            $jobStatusId = $jobStatus->getJobStatusId();

            $jobStatus = (new JobStatusRepository())->scopeOwner()->findOrFail($jobStatusId);

            // Set status interrupted if exist or cancel
            if ($jobStatus->interrupt && $data['status'] === JobStatus::STATUS_FINISHED) {
                $data['status'] = JobStatus::STATUS_INTERRUPTED;
            } else  if (($jobStatus->cancel && $data['status'] === JobStatus::STATUS_FINISHED) || $data['status'] ===JobStatus::STATUS_CANCELED) {
                $data['status'] = JobStatus::STATUS_CANCELED;
            }

            // Try to add attempts to the data we're saving - this will fail
            // for some drivers since they delete the job before we can check
            try {
                $data['attempts'] = $job->attempts();
            } catch (Exception $e) {
            }

            return $jobStatus->update($data);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
