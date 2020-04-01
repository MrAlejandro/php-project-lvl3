<?php

namespace App\Forms;

use Closure;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Repositories\DomainRepository;
use Illuminate\Support\Facades\Validator;

class DomainCreateForm
{
    protected $redirectRoute;
    protected $pageUrl;
    protected $errors;

    public function __construct(Request $request)
    {
        $this->pageUrl = $request->page_url;
        $this->domainName = parse_url($this->pageUrl, PHP_URL_HOST);
        $this->redirectRoute = '/';
        $this->errors = collect([]);
    }

    public function isValid(): bool
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

    public function showErrors(): void
    {
        $this->errors->each(function ($errorMessage) {
            flash($errorMessage)->error();
        });
    }

    public function create(): Domain
    {
        $domainData = collect(['name' => $this->domainName]);
        $domain = Domain::initializeWith($domainData);
        $domain = DomainRepository::store($domain);

        return $domain;
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function getRedirectRoute(): string
    {
        return $this->redirectRoute;
    }

    private function validateDomainNameUniqueness($attribute, $value, $fail): void
    {
        $domain = DomainRepository::findByDomainName($value);

        if (empty($domain)) {
            return;
        }

        $this->redirectRoute = route('domains.show', $domain);
        $nonUniqueDomainErrorMessage = __('domains.already_exists');
        $fail($nonUniqueDomainErrorMessage);
    }
}
