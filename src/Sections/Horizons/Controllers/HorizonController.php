<?php

namespace NetLinker\FairQueue\Sections\Horizons\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;
use NetLinker\FairQueue\Sections\Horizons\Resources\Horizon;
use NetLinker\FairQueue\Sections\Horizons\Requests\StoreHorizon;

class HorizonController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var HorizonRepository $horizons */
    protected $horizons;

    /**
     * Constructor
     *
     * @param HorizonRepository $horizons
     */
    public function __construct(HorizonRepository $horizons)
    {
        $this->horizons = $horizons;
    }

    /**
     * Request index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.horizons.index', [
            'h1' => __('fair-queue::horizons.horizons'),
            'horizons' => $this->scope($request)->response()->getData(),
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
        return Horizon::collection(
            $this->horizons->scope($request)
                ->latest()->smartPaginate()
        );
    }



    /**
     * Request store
     *
     * @param StoreHorizon $request
     * @return array
     */
    public function store(StoreHorizon $request)
    {
        $this->horizons->create($request->all());
        return notify(__('fair-queue::horizons.new_horizon_was_successfully_added'));
    }

    /**
     * Update
     *
     * @param StoreHorizon $request
     * @param $id
     * @return array
     */
    public function update(StoreHorizon $request, $id)
    {
        $this->horizons->update($request->all(), $id);

        return notify(
            __('fair-queue::horizons.horizon_was_successfully_updated'),
            new Horizon($this->horizons->findOrFail($id))
        );
    }

    /**
     * Destroy
     *
     * @param StoreHorizon $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $this->horizons->destroy($id);

        return notify(__('fair-queue::horizons.horizon_was_successfully_deleted'));
    }

}
