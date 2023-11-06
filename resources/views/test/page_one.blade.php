<x-easyadmin::guest-layout>
    <div class="bg-base-300">
        <div>This is page one</div>
        <div class="flex flex-row justify-evenly">
            <a href="" class="btn btn-primary btn-sm"
                @click.prevent.stop="$dispatch('linkaction', {link: '{{route('home')}}', route: 'home', fragment: 'page-content'});">
                Home
            </a>

            <a href="" class="btn btn-primary btn-sm"
                @click.prevent.stop="$dispatch('linkaction', {link: '{{route('pages.two')}}', route: 'pages.two', fragment: 'page-content'});">
                Page Two
            </a>
        </div>
    </div>
</x-easyadmin::guest-layout>
