<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomain;
use App\Services\PageAnalysisService;
use App\Repositories\DomainCheck;
use App\Repositories\DomainRepository;

class DomainController extends Controller
{
    public function index()
    {
        $domains = DomainRepository::all();
        $domainIds = $domains->pluck('id')->toArray();
        $domainChecks = DomainCheck::latestForDomains($domainIds)->keyBy('domain_id');

        return view('domains.index', ['domains' => $domains, 'domainChecks' => $domainChecks]);
    }

    public function show(int $id)
    {
        $domain = DomainRepository::findOrFail($id);
        $domainChecks = DomainCheck::allForDomainNewerFirst($id);

        return view('domains.show', ['domain' => $domain, 'domainChecks' => $domainChecks]);
    }

    public function store(StoreDomain $request)
    {
        $domainName = $request->domain['name'];
        $domainId = DomainRepository::create($domainName);

        return redirect()->route('domains.show', ['domain' => $domainId]);
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
