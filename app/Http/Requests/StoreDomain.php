<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\DomainRepository;
use Closure;

class StoreDomain extends FormRequest
{
    protected $redirect;

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'domain' => [
                'url'  => $this->domain['url'],
                'name' => parse_url($this->domain['url'], PHP_URL_HOST),
            ],
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $errors = collect($validator->errors());
            $errors->flatten()->each(function ($errorMessage) {
                flash($errorMessage)->error();
            });
        });
    }

    public function rules()
    {
        return [
            'domain.url' => 'bail|required|url',
            'domain.name' => [
                'sometimes',
                'nullable',
                Closure::fromCallable([$this, 'validateDomainNameUniqueness']),
            ],
        ];
    }

    public function validateDomainNameUniqueness($attribute, $value, $fail)
    {
        $domain = DomainRepository::findByDomainName($value);

        if (empty($domain)) {
            return;
        }

        $this->redirect = route('domains.show', ['domain' => $domain->id]);
        $nonUniqueDomainErrorMessage = __('domains.already_exists');
        $fail($nonUniqueDomainErrorMessage);
    }

    public function messages()
    {
        return [
            'domain.url.required' => __('domains.url.required'),
            'domain.url.url'  => __('domains.url.invalid'),
        ];
    }
}
