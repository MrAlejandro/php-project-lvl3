<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzePageJob;
use App\Repositories\DomainRepository;

class DomainCheckController extends Controller
{
    public function store(int $domainId)
    {
        $domain = DomainRepository::findOrFail($domainId);
        AnalyzePageJob::dispatch($domain->id);

        flash(__('domains.analysis_was_scheduled'))->success();

        return redirect()->route('domains.show', $domain);
    }
}
