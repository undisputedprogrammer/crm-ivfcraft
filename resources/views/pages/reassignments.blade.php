<x-easyadmin::app-layout>
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black px-8">
        <x-sections.side-drawer/>
        {{-- page body --}}

        <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
          <h2 class=" text-lg font-semibold text-primary bg-base-200">Re-assigned Leads</h2>
        </div>

        @if ($success)
        {{-- {{dd($reassignments)}} --}}
            <div>
                <table class="table table-sm  rounded-lg text-base-content">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Creation Date</th>
                            <th>Lead Segment</th>
                            <th>Original Assigned</th>
                            <th>Currently Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($reassignments as $l)
                        <tr>
                            <td>{{$l['lead']->name}}</td>
                            <td>{{$l['lead']->email}}</td>
                            <td>{{$l['lead']->phone}}</td>
                            <td>{{$l['created_at']}}</td>
                            <td>{{$l['lead']->customer_segment}}</td>
                            <td>{{$l['original_assigned']->name}}</td>
                            <td>{{$l['current_assigned']->name}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div>

        </div>
    </div>
</x-easyadmin::app-layout>
