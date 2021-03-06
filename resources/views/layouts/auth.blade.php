<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title>
    <script>
        var TS = {
            API:'{{ $routes["api"] }}',
            USER:{!! json_encode($TS) !!},
            MID: "{{ $TS['id'] or 0 }}",
            TOKEN: "{{ $token or '' }}",
            SITE_URL: "{{ getenv('APP_URL') }}",
            RESOURCE_URL: '{{ asset('assets/pc/') }}',
            CONFIG: {!! json_encode($config) !!},
            BOOT: {!! json_encode($config['bootstrappers']) !!},
            UNREAD: {}
        };
    </script>
    <link rel="stylesheet" href="{{ asset('assets/pc/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/pc/css/passport.css') }}">
    <script src="{{ asset('assets/pc/js/jquery.min.js') }}"></script>
</head>

<body @yield('body_class')>

    <div class="wrap">
        {{-- 导航 --}}
        @include('pcview::layouts.partials.authnav')

        {{-- 提示框 --}}
        <div class="noticebox authnoticebox">
        </div>

        {{-- 内容 --}}
        <div class="main">
        @yield('content')
        </div>
    </div>

    {{-- 底部 --}}
    @include('pcview::layouts.partials.authfooter')
    <script src="{{ asset('assets/pc/js/axios.min.js')}}"></script>
    <script src="{{ asset('assets/pc/js/common.js') }}"></script>
    <script src="{{ asset('assets/pc/js/iconfont.js') }}"></script>
    <script src="{{ asset('assets/pc/js/jquery.cookie.js') }}"></script>
    @yield('scripts')

    {{-- 统计代码 --}}
    @if (isset($config['common']['stats_code']) && $config['common']['stats_code'] != '')
    {!! $config['common']['stats_code'] !!}
    @endif
</body>

</html>
