<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} s</title>

    <!-- Styles -->
    {{--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">--}}
    <link href="{{ asset('css/front.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-dark bg-dark justify-content-between">

        <div class="navbar-header">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

        </div>
        </nav>


        <div class="card-body">
            <form action="{{ route('front.BillingImport') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <div class="col-auto">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="autoSizingCheck" name="judul">
                        <label class="form-check-label" for="autoSizingCheck">
                            judul
                        </label>
                    </div>
                </div>
                <button class="btn btn-success">Import User Data</button>
                {{--<a class="btn btn-warning" href="{{ route('export') }}">Export User Data</a>--}}
            </form>
        </div>
        @yield('content')
    </div>
</body>
</html>
