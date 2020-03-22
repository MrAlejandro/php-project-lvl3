<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomain;
use App\Repositories\DomainCheck;
use App\Repositories\Domain;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::all();
        $domainIds = $domains->pluck('id')->toArray();
        $domainChecks = DomainCheck::latestForDomains($domainIds)->keyBy('domain_id');

        return view('domains.index', ['domains' => $domains, 'domainChecks' => $domainChecks]);
    }

    public function show(int $id)
    {
        $domain = Domain::findOrFail($id);
        $domainChecks = DomainCheck::allForDomainNewerFirst($id);

        return view('domains.show', ['domain' => $domain, 'domainChecks' => $domainChecks]);
    }

    public function store(StoreDomain $request)
    {
        $domainName = $request->domain['name'];
        $domainId = Domain::create($domainName);

        return redirect()->route('domains.show', ['domain' => $domainId]);
    }

    public function check(int $id)
    {
        $domain = Domain::findOrFail($id);
        DomainCheck::create($domain->id);
        flash(__('domains.has_been_checked'));

        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}
