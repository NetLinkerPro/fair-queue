<?php


namespace NetLinker\FairQueue\Sections\JobStatuses\BladeDirectives;


use Illuminate\Support\Facades\Blade;

trait JobStatuses
{

    public function bladeDirectiveJobStatusBoot(){

        Blade::directive('jobstatuses', function ($config) {
            return "<?php echo view('fair-queue::sections.job-statuses.ajax',['config'=>$config])->render(); ?>";
        });

    }
}