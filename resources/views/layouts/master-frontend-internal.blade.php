@extends('oxygen::layouts.master-frontend')

@section('pageTitle', $pageTitle)

@section('contents')
    <div class="internal-page">

        <header>
            <aside class="bg-dark">
                <div class="container text-center">
                    <h2>{{ $pageTitle }}</h2>
                    <hr class="primary">
                </div>
            </aside>
        </header>

        <section id="contents">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('internal-page-contents')
                    </div>
                </div>
            </div>
        </section>

    </div>
@stop
