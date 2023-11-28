@props(['leads'])

{{-- {{dd($leads[0])}} --}}
<div x-data="{
        theLeads: []
    }" class=" w-[96%] mx-auto md:w-[45%] overflow-x-scroll hide-scroll">
    <div class="overflow-x-auto border border-primary rounded-xl overflow-y-scroll h-[65vh] hide-scroll">


        @if ($leads != null && count($leads) > 0)

            <table class="table table-sm table-compact">
                <!-- head -->
                <thead>
                    <tr class=" text-secondary sticky top-0 bg-base-300">
                        <th><input id="select-all" type="checkbox" class=" checkbox checkbox-secondary" @click="selectAll($el);"></th>
                        {{-- <th>ID</th> --}}
                        <th>Name</th>
                        <th>Campaign</th>
                        <th>Segment</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($leads as $lead)
                        <tr x-data="{ questions: null }" class="text-base-content hover:bg-base-100 cursor-pointer py-0"
                            :class=" lead.id == `{{ $lead->id }}` ? 'bg-base-100 font-medium' : ''"
                            @click="
                                $dispatch('detailsupdate',{lead : {{ json_encode($lead) }}, remarks: {{ json_encode($lead->remarks) }}, id: {{ $lead->id }}, followups: {{ $lead->followups }}, qnas: {{ json_encode($lead->qnas) }}})">

                            <th><input type="checkbox" :checked="selectedLeads[{{$lead->id}}] != null ? true : false " @click="selectLead($el,{{$lead}})" class="checkbox checkbox-secondary checkbox-sm individual-checkboxes py-1"></th>

                            {{-- <th>{{ $lead->id }}</th> --}}
                            <td id="name-{{$lead->id}}" class=" py-1">
                                <div class="flex flex-col">
                                    <span>{{ $lead->name }}</span>
                                    <span class=" text-info-content text-xs">{{ $lead->city }}</span>
                                </div>
                            </td>
                            <td id="campaign-{{$lead->id}}" class=" py-1">{{ $lead->campaign != '' ? $lead->campaign : '---' }}</td>
                            <td id="segment-{{$lead->id}}" :class="'{{$lead->customer_segment}}' != '' ? ' uppercase' : ''" class="py-1 uppercase">{{ $lead->customer_segment != null ? $lead->customer_segment : '---' }}</td>

                            <td class="py-1  text-warning" id="status-{{$lead->id}}">{{$lead->status}}</td>

                            <td class="py-1">{{$lead->created_at->format('d M Y')}}</td>

                            <td class=" py-1">
                                <div id="lead-tick-{{$lead->id}}" class="flex justify-center items-center p-0 h-4 w-4 rounded-full bg-success text-base-100 hidden">
                                <x-easyadmin::display.icon icon="easyadmin::icons.tick"
                                    height="h-4" widht="h-4" />
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
