<?php

namespace NetLinker\FairQueue\Sections\Supervisors\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use NetLinker\FairQueue\Sections\Supervisors\Repositories\SupervisorRepository;
use NetLinker\FairQueue\Sections\Supervisors\Resources\Supervisor;
use NetLinker\FairQueue\Sections\Supervisors\Requests\StoreSupervisor;

class SupervisorController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var SupervisorRepository $supervisors */
    protected $supervisors;

    /**
     * Constructor
     *
     * @param SupervisorRepository $supervisors
     */
    public function __construct(SupervisorRepository $supervisors)
    {
        $this->supervisors = $supervisors;
    }

    /**
     * Request index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.supervisors.index', [
            'h1' => __('fair-queue::supervisors.supervisors'),
            'supervisors' => $this->scope($request)->response()->getData(),
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
        return Supervisor::collection(
            $this->supervisors->scope($request)
                ->latest()->smartPaginate()
        );
    }



    /**
     * Request store
     *
     * @param StoreSupervisor $request
     * @return array
     */
    public function store(StoreSupervisor $request)
    {
        $this->supervisors->create($request->all());
        return notify(__('fair-queue::supervisors.new_supervisor_was_successfully_added'));
    }

    /**
     * Update
     *
     * @param StoreSupervisor $request
     * @param $id
     * @return array
     */
    public function update(StoreSupervisor $request, $id)
    {
        $this->supervisors->update($request->all(), $id);

        return notify(
            __('fair-queue::supervisors.supervisor_was_successfully_updated'),
            new Supervisor($this->supervisors->findOrFail($id))
        );
    }

    /**
     * Destroy
     *
     * @param StoreSupervisor $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $this->supervisors->destroy($id);

        return notify(__('fair-queue::supervisors.supervisor_was_successfully_deleted'));
    }

}
