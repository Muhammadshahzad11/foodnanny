<!DOCTYPE html>
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/iconly/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/calistoga/calistoga.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/public/public.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/rubik/rubik.css') }}">

    <!-- CUSTOM STYLE -->
    <link rel="stylesheet" href="{{ asset('themes/default/css/chat-file.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">

    <!-- PAGE TITLE -->
    <title>{{ Dipokhalder\Settings\Facades\Settings::group('company')->get('company_name') }}</title>
    <link rel="icon" type="image" href="{{ $favicon }}">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- THEME COLORS (admin configurable) -->
    <style>
        :root {
            --primary: {{ $primaryColor ?? '20 138 60' }};
            --secondary: {{ $secondaryColor ?? '10 61 40' }};
        }
    </style>

    @laravelPWA

    @if (!blank($analytics))
        @foreach ($analytics as $analytic)
            @if (!blank($analytic->analyticSections))
                @foreach ($analytic->analyticSections as $section)
                    @if ($section->section == \App\Enums\AnalyticSection::HEAD)
                        {!! $section->data !!}
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
</head>
<body>

@if (!blank($analytics))
    @foreach ($analytics as $analytic)
        @if (!blank($analytic->analyticSections))
            @foreach ($analytic->analyticSections as $section)
                @if ($section->section == \App\Enums\AnalyticSection::BODY)
                    {!! $section->data !!}
                @endif
            @endforeach
        @endif
    @endforeach
@endif

<div id="app"></div>

@if (!blank($analytics))
    @foreach ($analytics as $analytic)
        @if (!blank($analytic->analyticSections))
            @foreach ($analytic->analyticSections as $section)
                @if ($section->section == \App\Enums\AnalyticSection::FOOTER)
                    {!! $section->data !!}
                @endif
            @endforeach
        @endif
    @endforeach
@endif

<script>
    var apiUrl        = "{{ env('VITE_HOST') }}";
    var apiKey        = "{{ env('VITE_API_KEY') }}";
    var googleMapKey  = "{{ env('VITE_GOOGLE_MAP_KEY') }}";
    var pusherKey     = "{{ env('VITE_PUSHER_APP_KEY') }}";
    var pusherCluster = "{{ env('VITE_PUSHER_APP_CLUSTER') }}";
    var timezone      = "{{ env('VITE_TIMEZONE') }}";
    var demo          = "{{ env('VITE_DEMO') }}";
</script>

@if(env('VITE_GOOGLE_MAP_KEY'))
    <script>
        const mapKey = "{{ env('VITE_GOOGLE_MAP_KEY') }}";
    </script>
    <script>(g => {
            var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__",
                m = document, b = window;
            b = b[c] || (b[c] = {});
            var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
                    await (a = m.createElement("script"));
                    e.set("libraries", [...r] + "");
                    for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                    e.set("callback", c + ".maps." + q);
                    a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                    d[q] = f;
                    a.onerror = () => h = n(Error(p + " could not load."));
                    a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                    m.head.append(a)
                }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({key: mapKey, v: "weekly"});</script>
@endif
<script src="{{ asset('themes/default/js/customScript.js') }}"></script>
</body>
</html>
