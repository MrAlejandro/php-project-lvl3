<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Http\Requests\StoreDomain;
use App\Services\PageAnalysisService;
use App\Repositories\DomainRepository;
use App\Repositories\DomainCheckRepository;

class DomainController extends Controller
{
    public function index()
    {
        $domains = DomainRepository::all();
        $domainIds = $domains->pluck('id')->toArray();
        $domainChecks = DomainCheckRepository::latestForDomains($domainIds)->keyBy('domain_id');

        return view('domains.index', ['domains' => $domains, 'domainChecks' => $domainChecks]);
    }

    public function show(int $id)
    {
        $domain = DomainRepository::findOrFail($id);
        $domainChecks = DomainCheckRepository::allForDomainNewerFirst($id);

        return view('domains.show', ['domain' => $domain, 'domainChecks' => $domainChecks]);
    }

    public function store(StoreDomain $request)
    {
        $domainData = ['name' => $request->domain['name']];
        $domain = Domain::fromArray($domainData);
        $domain->save();

        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }

    public function check(int $id)
    {
        $domain = DomainRepository::findOrFail($id);
        $result = PageAnalysisService::analyze($domain);

        if ($result) {
            flash(__('domains.has_been_checked'))->success();
        } else {
            flash(__('domains.something_went_wrong'))->error();
        }

        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}
