<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Forms\DomainCreateForm;
use App\Services\PageAnalysisService;
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
        $domainChecks = DomainCheckRepository::allForDomainNewerFirst($id);

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
        return redirect($domainCreateForm->redirectUrl);
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
