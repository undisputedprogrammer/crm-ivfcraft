@props(['leads'])

{{-- {{dd($leads[0])}} --}}
<div x-data="{
        theLeads: []
    }" class=" w-[96%] mx-auto md:w-[45%] overflow-x-scroll hide-scroll">
    <div class="overflow-x-auto border border-primary rounded-xl overflow-y-scroll h-[65vh] hide-scroll">


        @if ($leads != null && count($leads) > 0)

            <table class="table table-sm">
                <!-- head -->
                <thead>
                    <tr class=" text-secondary sticky top-0 bg-base-300">
                        <th><input id="select-all" type="checkbox" class=" checkbox checkbox-secondary" @click="selectAll($el);"></th>
                        {{-- <th>ID</th> --}}
                        <th>Name</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($leads as $lead)
                        <tr x-data="{ questions: null }" class="text-base-content hover:bg-base-100 cursor-pointer"
                            :class=" lead.id == `{{ $lead->id }}` ? 'bg-base-100 font-medium' : ''"
                            @click="
                                $dispatch('detailsupdate',{lead : {{ json_encode($lead) }}, remarks: {{ json_encode($lead->remarks) }}, id: {{ $lead->id }}, followups: {{ $lead->followups }}, qnas: {{ json_encode($lead->qnas) }}})">

                            <th><input type="checkbox" :checked="selectedLeads[{{$lead->id}}] != null ? true : false " @click="selectLead($el,{{$lead}})" class="checkbox checkbox-secondary checkbox-sm individual-checkboxes"></th>

                            {{-- <th>{{ $lead->id }}</th> --}}
                            <td id="name-{{$lead->id}}">{{ $lead->name }}</td>
                            <td id="city-{{$lead->id}}">{{ $lead->city }}</td>
                            <td id="phone-{{$lead->id}}">{{ $lead->phone }}</td>
                            <td>
                                <div id="lead-tick-{{$lead->id}}" class="flex justify-center items-center p-0 h-7 w-7 rounded-full bg-success text-base-100 hidden">
                                <x-easyadmin::display.icon icon="easyadmin::icons.tick"
                                    height="h-6" widht="h-6" />
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No leads to show</h1>
        @endif


    </div>
    <div class="mt-1.5">
        {{ $leads->links() }}
    </div>




</div>
