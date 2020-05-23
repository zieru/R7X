<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} Connect</title>
    <link href="{{ asset('css/_base0.min.css') }}" rel="stylesheet">
    <style>

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
        .btn-google {
            color: #545454;
            background-color: #ffffff;
            box-shadow: 0 1px 2px 1px #ddd
        }
    </style>
</head>
<body>
<div class="login col-lg-4 mx-auto">
    <div class="card mx-auto" style="min-width: 380px;">
        <div class="card-header bg-white">
            Sign in
        </div>
        <article class="card-body text-center">
            <h4 class="card-title mb-4 mt-1">{{ env('APP_NAME') }} Connect</h4>
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                   {{ Session::get('error')  }}
                </div>
            @endif
            <div class="alert alert-info" role="alert">
                You will be redirected to : </br>{{ Request::get('redirect') }}
            </div>
            @if (Session::has('token_data'))
                @php
                    $token_response = Session::get('token_data');
                @endphp
                <div class="alert alert-success" role="alert">
                    Login Success
                </div>
                <script>
                    //window.location = '/player_detail?access_token=' + {{ $token_response['access_token'] }}+'&?refresh_token='{{ $token_response['refresh_token'] }}+'&expires_in='+{{ $token_response['expires_in'] }};
                </script>
            @else
                <form method="POST" action="{{ route('api.v1.login')  }}">
                    <input type="hidden" name="redirect" value="{{ Request::fullUrl()}}"/>
                    <div class="form-group">
                        <input name="email" value="nazirul777@gmail.com" class="form-control" placeholder="Email or login" type="text">
                    </div> <!-- form-group// -->
                    <div class="form-group">
                        <input class="form-control" name="password" placeholder="******" type="password" value="zierong7">
                    </div> <!-- form-group// -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div> <!-- form-group// -->
                        </div>
                        <div class="col-md-6 text-right">
                            {{--<a class="small" href="#">Forgot password?</a>--}}
                        </div>
                    </div> <!-- .row// -->
                </form>
                <hr>

                <p>

                <div class="col-md-12"> <a class="btn btn-google btn-block text-uppercase btn-outline" href="{{ route('front.oauthlogin.provider.google')  }}"><img src="https://img.icons8.com/color/16/000000/google-logo.png"> Login Using Google</a> </div>
                </p>
            @endif
        </article>
    </div> <!-- card.// -->
</div>


</aside> <!-- col.// -->

</body>
</html>
