<x-easyadmin::guest-layout>
    <div>This is page two</div>
    <div class="flex flex-row justify-evenly">
        <a href="" class="btn btn-primary btn-sm"
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('home')}}', route: 'home', fragment: 'page-content'});">
            Home
        </a>
        <a href="" class="btn btn-primary btn-sm"
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('pages.one')}}', route: 'pages.one', fragment: 'page-content'});">
            Page One
        </a>
    </div>
</x-easyadmin::guest-layout>
