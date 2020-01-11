<?php

namespace Netlinker\FairQueue\Tests;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Artisan;

class SkeletonArchiveExtractorsTest extends TestCase
{


    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $config */
        $config = $app['config'];

        // Change the extension in github archive url form .zip to .tar.gz
        $originalZipUrl = $config->get('packager.skeleton');
        $tarGzUrl = str_replace('.zip', '.tar.gz', $originalZipUrl);

        $config->set('packager.skeleton', $tarGzUrl);
    }
}
