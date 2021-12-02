<?php

namespace NetLinker\FairQueue\Sections\Dashboard\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('fair-queue::sections.dashboard.index',
            [
                'h1' => __('fair-queue::dashboard.startup_baselinker'),
                'leadsChartData' =>[],
                'salesChartData' => [],
                'leadsComparisonChartData' => [],
                'leads' => [],
                'sales' =>  [],
            ]
        );
    }
}
