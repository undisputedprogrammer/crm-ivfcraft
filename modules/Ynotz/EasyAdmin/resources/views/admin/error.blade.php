<x-easyadmin::partials.adminpanel>
    <div class="flex flex-col justify-evenly items-center h-full">
        <span class="text-2xl text-base-content text-opacity-50">
            Unable to display the page.
            @if (config('app.debug'))
                <div class="text-sm p-4 max-w-2/3">Error:<br>{{$error}}</div>
            @endif
        </span>
        <div></div>
    </div>
</x-easyadmin::partials.adminpanel>
