<?php

namespace App\Forms;

use Closure;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Repositories\DomainRepository;
use Illuminate\Support\Facades\Validator;

class DomainCreateForm
{
    protected $errors = [];
    protected $redirectRoute = '/';

    protected $pageUrl;

    public function __construct(Request $request)
    {
        $this->pageUrl = $request->page_url;
        $this->domainName = parse_url($this->pageUrl, PHP_URL_HOST);
    }

    public function isValid()
    {
        $data = [
            'pageUrl' => $this->pageUrl,
            'domainName' => $this->domainName,
        ];

        $validator = Validator::make($data, [
            'pageUrl' => 'bail|required|url',
            'domainName' => [
                'sometimes',
                'nullable',
                Closure::fromCallable([$this, 'validateDomainNameUniqueness']),
            ],
        ]);

        if ($validator->fails()) {
            $this->errors = collect($validator->errors())->flatten();
            return false;
        }

        return true;
    }

    public function showErrors()
    {
        $this->errors->each(function ($errorMessage) {
            flash($errorMessage)->error();
        });
    }

    public function create()
    {
        $domainData = ['name' => $this->domainName];
        $domain = Domain::fromArray($domainData);
        $domain = DomainRepository::store($domain);

        return $domain;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    private function validateDomainNameUniqueness($attribute, $value, $fail)
    {
        $domain = DomainRepository::findByDomainName($value);

        if (empty($domain)) {
            return;
        }

        $this->redirectRoute = route('domains.show', ['domain' => $domain->id]);
        $nonUniqueDomainErrorMessage = __('domains.already_exists');
        $fail($nonUniqueDomainErrorMessage);
    }
}
