
<div class="w-full">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($followups != null && count($followups)>0)
        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              <th></th>
              <th>Name</th>
              <th>City</th>
              <th>Phone</th>
              @can('is-admin')
                  <th>Agent</th>
              @endcan
              <th>Date</th>
            </tr>
          </thead>
          <tbody>



            @foreach ($followups as $followup)

            {{-- fpupdate event is used to display followup detail to the details section --}}
                <tr class="text-neutral-content hover:bg-base-100" :class=" fpname == '{{$followup->lead->name}}' ? 'bg-base-100 font-medium' : '' " @click.prevent.stop="


                    $dispatch('dataupdate',{followup : {{$followup}}, lead: {{$followup->lead}}, remarks: {{$followup->remarks}}, id: {{$followup->id}}, lead_remarks: {{json_encode($followup->lead->remarks)}}})"
                    >
                    <th>{{$followup->id}}</th>
                    <td>{{$followup->lead->name}}</td>
                    <td>{{$followup->lead->city}}</td>
                    <td>{{$followup->lead->phone}}</td>
                    @can('is-admin')
                        <td>{{$followup->lead->assigned->name}}</td>
                    @endcan
                    <td>{{date_format($followup->updated_at, 'd-m-Y')}}</td>
                </tr>
            @endforeach




          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No results found</h1>
        @endif


      </div>
      <div class="mt-1.5">
        {{ $followups->links() }}
    </div>
</div>
