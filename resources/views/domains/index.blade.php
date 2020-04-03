@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h1 class="mb-5">{{ __('domains.domains') }}</h1>
        <table class="table">
            <tbody>
                <tr>
                    <th>{{ __('domains.id') }}</th>
                    <th>{{ __('domains.name') }}</th>
                    <th>{{ __('domains.status_code') }}</th>
                </tr>
                @foreach ($domains as $domain)
                    <tr>
                        <td>{{ $domain->id }}</td>
                        <td><a href="{{ route('domains.show', $domain) }}">https://{{ $domain->name }}</a></td>
                        @if (empty($domainChecks[$domain->id]))
                            <td>&mdash;</td>
                        @else
                            <td>{{ $domainChecks[$domain->id]->statusCode }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody></table>
        </table>
    </div>
@endsection
