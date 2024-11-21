<x-easyadmin::app-layout>
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">
        {{-- <x-sections.side-drawer/> --}}
        {{-- page body --}}

        <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
          <h2 class=" text-lg font-semibold text-primary bg-base-200">Re-assigned Leads</h2>
        </div>

        @if ($success)
        {{-- {{dd($reassignments)}} --}}
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Lead Segment</th>
                            <th>Original Assigned</th>
                            <th>Currently Assigned</th>
                        </tr>
                    </thead>
                </table>
                <tbody>
                @foreach ($reassignments as $l)
                    <tr>
                        <td>{{$l['name']}}</td>
                        <td>{{$l['phone']}}</td>
                        <td>{{$l['email']}}</td>
                        <td>{{$l['customer_segment']}}</td>
                        <td>{{$original_assigned['name']}}</td>
                        <td>{{$current_assigned['name']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </div>
        @endif
        <div>

        </div>
    </div>
</x-easyadmin::app-layout>
