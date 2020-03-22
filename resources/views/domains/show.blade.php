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
                    <td>{{ \App\Helpers\DateHelper::format($domain->created_at) }}</td>
                </tr>
                <tr>
                    <td>{{ __('domains.updated_at') }}</td>
                    <td>{{ \App\Helpers\DateHelper::format($domain->updated_at) }}</td>
                </tr>
            </tbody>
        </table>
        <h2 class="mb-3">{{ __('domains.checks') }}</h2>
        <form method="post" action="{{ route('domains.check', ['id' => $domain->id]) }}">
            @csrf
            <input type="submit" class="btn btn-primary" value="{{ __('domains.run_check') }}">
        </form>
        <table class="table">
            <tbody>
                @foreach ($domainChecks as $domainCheck)
                    <tr>
                        <td>{{ $domainCheck->status_code }}</td>
                        <td>{{ \App\Helpers\DateHelper::format($domainCheck->updated_at) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
