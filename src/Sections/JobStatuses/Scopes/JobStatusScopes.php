<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Scopes;

use AwesIO\Repository\Scopes\ScopesAbstract;

class JobStatusScopes extends ScopesAbstract
{
    protected $scopes = [
'job_statuses_ajax' => JobStatusesAjaxScope::class,
    ];
}