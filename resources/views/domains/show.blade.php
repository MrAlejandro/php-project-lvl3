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
        {{-- <h1 class="display-3">{{ __("domains.page_analyzer") }}</h1> --}}
        {{-- <p class="lead">{{ __("domains.check_pages_for_free") }}</p> --}}
        {{-- <hr class="my-4"> --}}

        {{-- <form action="http://php-l3-page-analyzer.herokuapp.com/domains" method="post" class="d-flex justify-content-center form-inline"> --}}
        {{--     @csrf --}}
        {{--     <input type="text" name="domain[name]" class="form-control form-control-lg" placeholder="https://www.example.com"> --}}
        {{--     <button type="submit" class="btn btn-lg btn-primary ml-3">{{ __("domains.add") }}</button> --}}
        {{-- </form> --}}
    </div>
@endsection
