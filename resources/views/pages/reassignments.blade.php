<x-easyadmin::app-layout>
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">
        {{-- <x-sections.side-drawer/> --}}
        {{-- page body --}}

        <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
          <h2 class=" text-lg font-semibold text-primary bg-base-200">Re-assigned Leads</h2>
        </div>

        @if ($success)
        {{dd($reassignments)}}
            <div>
                {{-- @foreach ($reassignments as $)

                @endforeach --}}
            </div>
        @endif
        <div>

        </div>
    </div>
</x-easyadmin::app-layout>
