<?php

namespace NetLinker\FairQueue\Sections\Accesses\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use NetLinker\FairQueue\Sections\Accesses\Repositories\AccessRepository;
use NetLinker\FairQueue\Sections\Accesses\Requests\StoreAccess;
use NetLinker\FairQueue\Sections\Accesses\Resources\Access;

class AccessController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var AccessRepository $accesses */
    protected $accesses;

    /**
     * Constructor
     *
     * @param AccessRepository $accesses
     */
    public function __construct(AccessRepository $accesses)
    {
        $this->accesses = $accesses;
    }

    /**
     * Request index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.accesses.index', [
            'h1' => __('fair-queue::accesses.accesses'),
            'accesses' => $this->scope($request)->response()->getData(),
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
        return Access::collection(
            $this->accesses->scope($request)
                ->latest()->smartPaginate()
        );
    }



    /**
     * Request store
     *
     * @param StoreAccess $request
     * @return array
     */
    public function store(StoreAccess $request)
    {
        $this->accesses->create($request->all());
        return notify(__('fair-queue::accesses.new_access_was_successfully_added'));
    }

    /**
     * Update
     *
     * @param StoreAccess $request
     * @param $id
     * @return array
     */
    public function update(StoreAccess $request, $id)
    {
        $this->accesses->update($request->all(), $id);

        return notify(
            __('fair-queue::accesses.access_was_successfully_updated'),
            new Access($this->accesses->findOrFail($id))
        );
    }

    /**
     * Destroy
     *
     * @param StoreAccess $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $this->accesses->destroy($id);

        return notify(__('fair-queue::accesses.access_was_successfully_deleted'));
    }

    /**
     * Objects
     *
     * @param StoreAccess $request
     * @return array
     */
    public function objects(Request $request)
    {
        return $this->accesses->getQueueObjects($request->queue_uuid, $request->q);
    }
}
