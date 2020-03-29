<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainCheck;
use App\Forms\DomainCreateForm;
use App\Services\PageAnalysisService;
use App\Services\PageFetchingService;
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

        $fetchResult = PageFetchingService::fetch($domain->name);
        if ($fetchResult->isSuccessful()) {
            $body = $fetchResult->getResult()->get('body');
            $analysisResult = PageAnalysisService::analyze($body);

            $domainCheck = $analysisResult
                ->getResult()
                ->merge($fetchResult->getResult()->except('body'))
                ->put('domainId', $domain->id);

            $domainCheck = DomainCheck::fromArray($domainCheck->toArray());
            DomainCheckRepository::store($domainCheck);

            flash(__('domains.has_been_checked'))->success();
        } else {
            flash(__('domains.something_went_wrong'))->error();
        }

        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}
