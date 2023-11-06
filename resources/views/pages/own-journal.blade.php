<x-easyadmin::app-layout>

    <div>
        <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black">


            <x-sections.side-drawer />
            {{-- page body --}}
            <div class="w-full bg-base-200">
                <h1 class="p-4 text-primary text-xl font-semibold bg-base-200 ">Journal</h1>
                <div class="md:max-w-2/3 flex m-auto my-8 text-base-content opacity-80 border border-base-content border-opacity-20 rounded-lg overflow-hidden">
                    {{-- {{dd($journals)}} --}}
                    <table class="table table-zebra table-compact">
                        <thead>
                            <tr class="bg-base-300 text-secondary">
                                <th>Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journals as $j)
                                <tr>
                                    <td>{{$j->date}}</td>
                                    <td>{!!nl2br(e($j->body))!!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
    <x-footer />
</x-easyadmin::app-layout>
