@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <div class="jumbotron">
            <h1 class="display-3">{{ __("domains.page_analyzer") }}</h1>
            <p class="lead">{{ __("domains.check_pages_for_free") }}</p>
            <hr class="my-4">

            <form action="{{ route('domains.store') }}" method="post" class="d-flex justify-content-center form-inline">
                @csrf
                <input type="text" name="domain[url]" class="form-control form-control-lg" placeholder="https://www.example.com">
                <button type="submit" class="btn btn-lg btn-primary ml-3">{{ __("domains.add") }}</button>
            </form>
        </div>
    </div>
@endsection
