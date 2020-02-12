<?php

namespace NetLinker\FairQueue\Sections\Assets\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get resource AWES
     *
     * @return \Illuminate\Http\Response
     */
    public function getAwes(Request $request, $module, $type, $filename)
    {
        $path = base_path('vendor/awes-io/' . $module . '/dist/' .$type.'/'. $filename);
        $content = File::get($path);

        if ( Str::endsWith($filename, '.js')){
            return Response::make($content, 200)->header('Content-Type', 'application/javascript');
        } else   if ( Str::endsWith($filename, '.css')){
            return Response::make($content, 200)->header('Content-Type', 'text/css');
        }

        return Response::make($content, 200)->header('Content-Type', File::mimeType($path));
    }

}
