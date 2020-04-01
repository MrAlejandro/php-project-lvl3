@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h1 class="mb-5">https://{{ $domain->name }}</h1>
        <table class="table">
            <tbody>
                <tr>
                    <td>{{ __('domains.id') }}</td>
                    <td>{{ $domain->id }}</td>
                </tr>
                <tr>
                    <td>{{ __('domains.name') }}</td>
                    <td>https://{{ $domain->name }}</td>
                </tr>
                <tr>
                    <td>{{ __('domains.created_at') }}</td>
                    <td>{{ \App\Helpers\DateHelper::format($domain->createdAt) }}</td>
                </tr>
                <tr>
                    <td>{{ __('domains.updated_at') }}</td>
                    <td>{{ \App\Helpers\DateHelper::format($domain->updatedAt) }}</td>
                </tr>
            </tbody>
        </table>
        <h2 class="mb-3">{{ __('domains.checks') }}</h2>
        <form method="post" action="{{ route('domains.checks.store', $domain) }}">
            @csrf
            <input type="submit" class="btn btn-primary" value="{{ __('domains.run_check') }}">
        </form>
        <table class="table">
            <thead>
                <th>{{ __('domain_checks.status_code') }}</th>
                <th>{{ __('domain_checks.date') }}</th>
                <th>{{ __('domain_checks.keywords') }}</th>
                <th>{{ __('domain_checks.description') }}</th>
                <th>{{ __('domain_checks.h1') }}</th>
            </thead>
            <tbody>
                @foreach ($domainChecks as $domainCheck)
                    <tr>
                        <td>{{ $domainCheck->statusCode }}</td>
                        <td>{{ \App\Helpers\DateHelper::format($domainCheck->updatedAt) }}</td>
                        <td>{{ $domainCheck->keywords }}</td>
                        <td>{{ $domainCheck->description }}</td>
                        <td>{{ $domainCheck->h1 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
