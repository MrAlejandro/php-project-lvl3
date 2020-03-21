<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomain;
use App\Repositories\Domain;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::all();

        return view('domains.index', ['domains' => $domains]);
    }

    public function show(int $id)
    {
        $domain = Domain::findOrFail($id);

        return view('domains.show', ['domain' => $domain]);
    }

    public function store(StoreDomain $request)
    {
        $domainName = $request->domain['name'];
        $domainId = Domain::create($domainName);

        return redirect()->route('domains.show', ['domain' => $domainId]);
    }
}
