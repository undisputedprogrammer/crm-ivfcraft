@props(['followups'])
<div class=" w-[96%] lg:w-[40%]">
    <div class="overflow-x-auto border border-primary rounded-xl overflow-y-scroll h-[65vh] hide-scroll">
        @if ($followups != null && count($followups)>0)
        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Name</th>
              <th>Campaign</th>
              <th>Segment</th>
            </tr>
          </thead>
          <tbody>



            @foreach ($followups as $followup)

            {{-- fpupdate event is used to display followup detail to the details section --}}
                <tr class="text-base-content hover:bg-base-100 cursor-pointer py-0" :class=" fpname == `{{$followup->lead->name}}` ? 'bg-base-100 font-medium' : '' " @click.prevent.stop="


                    $dispatch('fpupdate',{followup : {{json_encode($followup)}}, lead: {{json_encode($followup->lead)}}, remarks: {{json_encode($followup->remarks)}}, id: {{$followup->id}}, lead_remarks: {{json_encode($followup->lead->remarks)}}, appointment: {{json_encode($followup->lead->appointment)}}, qnas: {{json_encode($followup->lead->qnas)}} })"
                    >
                    {{-- <th>{{$followup->id}}</th> --}}
                    <td class=" py-1">
                        <div class=" flex flex-col">
                            <span>{{$followup->lead->name}}</span>
                            <span class=" text-info-content text-xs">{{$followup->lead->city}}</span>
                        </div>
                    </td>
                    <td class="py-1">{{$followup->lead->campaign != '' ? $followup->lead->campaign : 'Unknown'}}</td>
                    <td :class=" '{{$followup->lead->customer_segment}}' != '' ? 'uppercase' : '' " class=" py-1">{{$followup->lead->customer_segment ?? 'Unknown'}}</td>
                </tr>
            @endforeach




          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-base-content p-4">No follow ups for now</h1>
        @endif


      </div>
      <div class="mt-1.5">
        {{ $followups->links() }}
    </div>
</div>
