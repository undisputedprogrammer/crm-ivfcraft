<!DOCTYPE html>
<html x-data="{theme: $persist('newdark'), href: '', currentpath: '{{url()->current()}}', currentroute: '{{ Route::currentRouteName() }}'}"
@themechange.window="theme = $event.detail.darktheme ? 'newdark' : 'light';" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
x-init="window.landingUrl = '{{\Request::getRequestUri()}}'; window.landingRoute = '{{ Route::currentRouteName() }}'; window.renderedpanel = 'pagecontent';"
@pagechanged.window="
currentpath=$event.detail.currentpath;
currentroute=$event.detail.currentroute;"
@routechange.window="currentroute=$event.detail.route;"
:data-theme="theme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="initPage" x-init="initAction();"
        @linkaction.window="initialised = false; fetchLink($event.detail);"
        @popstate.window="historyAction($event)"
        class="font-sans antialiased text-sm transition-colors">
        <div class="min-h-screen bg-base-200 flex flex-col">
            @include('smartpages::layouts.navigation')
            <main class="flex flex-col items-stretch flex-grow w-full">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
