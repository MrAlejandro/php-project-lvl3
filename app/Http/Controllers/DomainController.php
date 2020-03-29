<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AnalyzePageJob;
use App\Forms\DomainCreateForm;
use App\Repositories\DomainRepository;
use App\Repositories\DomainCheckRepository;

class DomainController extends Controller
{
    public function index()
    {
        $domains = DomainRepository::all();
        $domainChecks = DomainCheckRepository::latestForDomains($domains)->keyBy('domainId');

        return view('domains.index', ['domains' => $domains, 'domainChecks' => $domainChecks]);
    }

    public function show(int $id)
    {
        $domain = DomainRepository::findOrFail($id);
        $domainChecks = DomainCheckRepository::allForDomainNewerFirst($domain);

        return view('domains.show', ['domain' => $domain, 'domainChecks' => $domainChecks]);
    }

    public function store(Request $request)
    {
        $domainCreateForm = new DomainCreateForm($request);

        if ($domainCreateForm->isValid()) {
            $domain = $domainCreateForm->create();
            return redirect()->route('domains.show', ['domain' => $domain->id]);
        }

        $domainCreateForm->showErrors();
        $redirectRoute = $domainCreateForm->getRedirectRoute();

        return redirect($redirectRoute);
    }

    public function check(int $id)
    {
        $domain = DomainRepository::findOrFail($id);
        AnalyzePageJob::dispatch($domain->id);

        flash(__('domains.analysis_was_scheduled'))->success();

        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}
