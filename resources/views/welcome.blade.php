<x-easyadmin::guest-layout>
    <div>This is home page</div>
    <div x-data class="flex flex-row justify-evenly">
        <a href="" class="btn btn-primary btn-sm"
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('pages.one')}}', route: 'pages.one', fragment: 'page-content'});">
            Page One
        </a>
        <a href="" class="btn btn-primary btn-sm"
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('pages.two')}}', route: 'pages.two', fragment: 'page-content'});">
            Page Two
        </a>
        <a href="" class="btn btn-primary btn-sm"
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('pages.three')}}', route: 'pages.three', fragment: 'page-content'});">
            Page Three
        </a>
    </div>
</x-easyadmin::guest-layout>
