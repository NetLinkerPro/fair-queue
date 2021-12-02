<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use NetLinker\FairQueue\Sections\JobStatuses\Repositories\JobStatusRepository;
use NetLinker\FairQueue\Sections\JobStatuses\Resources\JobStatus;
use NetLinker\LeadAllegro\Sections\Accounts\Jobs\ImportAuctionJob;
use NetLinker\LeadAllegro\Sections\Accounts\Requests\ImportAccount;
use NetLinker\LeadAllegro\Sections\Accounts\Requests\StoreAccount;

class JobStatusController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var JobStatusRepository $jobStatuses */
    protected $jobStatuses;

    /**
     * Constructor
     *
     * @param JobStatusRepository $jobStatuses
     */
    public function __construct(JobStatusRepository $jobStatuses)
    {
        $this->jobStatuses = $jobStatuses;
    }

    /**
     * Request index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.job-statuses.index', [
            'h1' => __('fair-queue::job-statuses.jobs'),
            'job_statuses' => $this->scope($request)->response()->getData(),
        ]);
    }

    /**
     * Request scope
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function scope(Request $request)
    {
        return JobStatus::collection(
            $this->jobStatuses->scope($request)
                ->scopeOwner()
                ->latest()->smartPaginate()
        );
    }


    /**
     * Request interrupt
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function interrupt(Request $request)
    {
        $this->jobStatuses->interrupt($request->id);
        return notify(__('fair-queue::job-statuses.job_status_was_successfully_interrupted'));
    }

    /**
     * Request cancel
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function cancel(Request $request)
    {

        $this->jobStatuses->cancel($request->id);
        return notify(__('fair-queue::job-statuses.job_status_was_successfully_canceled'));
    }

}
