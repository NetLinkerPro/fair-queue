<?php

namespace NetLinker\FairQueue\Sections\Queues\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use NetLinker\FairQueue\Sections\Queues\Repositories\QueueRepository;
use NetLinker\FairQueue\Sections\Queues\Requests\StoreQueue;
use NetLinker\FairQueue\Sections\Queues\Resources\Queue;

class QueueController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var QueueRepository $queues */
    protected $queues;

    /**
     * Constructor
     *
     * @param QueueRepository $queues
     */
    public function __construct(QueueRepository $queues)
    {
        $this->queues = $queues;
    }

    /**
     * Request index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.queues.index', [
            'h1' => __('fair-queue::queues.queues'),
            'queues' => $this->scope($request)->response()->getData(),
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
        return Queue::collection(
            $this->queues->scope($request)
                ->latest()->smartPaginate()
        );
    }



    /**
     * Request store
     *
     * @param StoreQueue $request
     * @return array
     */
    public function store(StoreQueue $request)
    {
        $this->queues->create($request->all());
        return notify(__('fair-queue::queues.new_queue_was_successfully_added'));
    }

    /**
     * Update
     *
     * @param StoreQueue $request
     * @param $id
     * @return array
     */
    public function update(StoreQueue $request, $id)
    {
        $this->queues->update($request->all(), $id);

        return notify(
            __('fair-queue::queues.queue_was_successfully_updated'),
            new Queue($this->queues->findOrFail($id))
        );
    }

    /**
     * Destroy
     *
     * @param StoreQueue $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $this->queues->destroy($id);

        return notify(__('fair-queue::queues.queue_was_successfully_deleted'));
    }

    /**
     * Model collection
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function modelCollection(Request $request)
    {
        $queueModels = $this->queues->getQueueModels($request->queue, $request->q);
        return response()->json($queueModels);
    }
}
