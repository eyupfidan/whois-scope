<!DOCTYPE html>
<html lang="{{ request()->route('locale') ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fast and secure domain whois lookup — single and bulk query support.">
    <meta name="theme-color" content="#0284c7">
    @php($localizedPage = request()->is('*/docs') ? '/docs' : '')
    @foreach (['en', 'tr', 'es', 'zh', 'ar', 'pt', 'fr'] as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ url('/'.$locale.$localizedPage) }}" data-locale-alternate>
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/en'.$localizedPage) }}" data-locale-alternate>
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <title>{{ config('app.name', 'WhoisScope') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <div id="app"></div>
</body>
</html>
