<x-easyadmin::app-layout>
    <div x-data="x_overview"
    x-init = "@if(isset($journal))
    journal = {{$journal}};
    @endif
    getParams();
    chartCanvas = document.getElementById('chartCanvas');
    validChartCanvas = document.getElementById('validChartCanvas');
    genuineChartCanvas = document.getElementById('genuineChartCanvas');
    @isset($process_chart_data)
        processChartData = JSON.parse('{{$process_chart_data}}');
    @endisset
    @isset($valid_chart_data)
        validChartData = JSON.parse('{{$valid_chart_data}}');
    @endisset
    @isset($genuine_chart_data)
        genuineChartData = JSON.parse('{{$genuine_chart_data}}');
    @endisset
    @isset($selectedCenter)
        selectedCenter = {{$selectedCenter}}
    @endisset
    initChart();
    "
    >
        <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">
            <x-sections.side-drawer />
            <!-- ./Header -->



            <div class=" min-h-[calc(100vh-3.5rem)] pb-[2.8rem] w-full mx-auto  bg-base-100 ">



                <div class="w-[96%] mx-auto rounded-xl bg-base-100 p-3  flex flex-col space-y-6">
                    <h1 class=" text-xl font-semibold text-primary ">Performance Analysis</h1>

                    <div>

                        <form @submit.prevent.stop="searchPerformance($el);" action="" id="performance-search-form" class="border border-opacity-30 rounded-lg p-2 text-base-content w-fit flex flex-col space-y-2">
                            <h1 class=" font-medium uppercase">Choose by date</h1>
                            <div class=" flex space-x-4">
                                <div class=" flex flex-col">
                                    <label  class=" text-primary font-medium">From :</label>
                                    <div x-data="{date: ''}" x-init="date = '{{$from}}';" class="relative">
                                        <input x-model="date" type="date" name="from" required value="{{$from}}" class=" input input-sm input-bordered border-primary">
                                        <div class="absolute top-0 left-0 z-30 w-4/5 bg-base-100 m-1" x-text="date.length > 0 ? formatDateOnly(date) : '_ _ _'"></div>
                                    </div>
                                </div>
                                <div class=" flex flex-col">
                                    <label  class=" text-primary font-medium">To :</label>
                                    <div x-data="{date: ''}" x-init="date = '{{$to}}';" class="relative">
                                        <input x-model="date" type="date" name="to" required value="{{$to}}" prevent="$el.value=formatDateOnly($el.value);" class="relative input input-sm input-bordered border-primary">
                                        <div class="absolute top-0 left-0 z-30 w-4/5 bg-base-100 m-1" x-text="date.length > 0 ? formatDateOnly(date) : '_ _ _'"></div>
                                    </div>
                                </div>

                                <div class=" flex flex-col">
                                    <label  class=" text-primary font-medium">Center :</label>
                                    <select name="center" id="select-center" class="select select-bordered select-primary select-sm text-xs">
                                        <option value="" :selected="selectedCenter == null">All centers</option>
                                        @foreach ($centers as $center)
                                            <option :selected="selectedCenter == {{$center->id}}" value="{{$center->id}}">{{$center->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class=" btn btn-sm btn-primary h-fit self-end">Search</button>

                                <button @click.prevent.stop="resetPerformancePage()" class=" btn btn-sm btn-ghost h-fit self-end">
                                    <x-icons.refresh-icon/>
                                </button>
                            </div>
                        </form>

                    </div>

                    <div x-show="isSearchResults" x-init="checkForParams()" class="font-semibold text-lg text-base-content">
                        Showing search results
                    </div>

                    <div
                        class="flex flex-col space-y-2 md:space-y-0 md:flex-row  md:space-x-3 justify-evenly md:items-center ">

                        <div
                            class="flex flex-col space-y-1 bg-base-200 w-full lg:w-1/4 h-16 rounded-xl justify-center items-center py-4">
                            <label
                                class=" font-medium text-primary w-[90%] flex justify-between items-center">
                                <span>Total leads this month</span>
                                <span class="text-lg font-semibold text-secondary">{{ $lpm }}</span>
                            </label>
                            {{-- <progress class="progress progress-success w-[90%] mx-auto" value="50" max="100"></progress> --}}
                        </div>

                        <div class="flex flex-col space-y-1 bg-base-200 w-full lg:w-1/4 rounded-xl items-center py-4">
                            <label  class=" font-medium text-primary w-[90%] flex justify-between">
                                <span>Lead followed up this month</span>
                                <span
                                    class=" text-base font-semibold text-secondary">{{ $ftm }}/{{ $lpm }}</span>
                            </label>
                            @php
                                if ($lpm != 0) {
                                    $perc_lf = ($ftm / $lpm) * 100;
                                } else {
                                    $perc_lf = 0;
                                }

                            @endphp
                            <progress class="progress progress-success w-[90%] mx-auto" value="{{ $perc_lf }}"
                                max="100"></progress>

                        </div>

                        <div class="flex flex-col space-y-1 bg-base-200 w-full lg:w-1/4 rounded-xl items-center py-4">
                            <label  class=" font-medium text-primary w-[90%] flex justify-between">
                                <span>Leads consulted this month</span>
                                @php
                                    if ($lpm != 0) {
                                        $ctm = $lcm / $lpm;
                                    } else {
                                        $ctm = 0;
                                    }
                                @endphp
                                <span
                                    class="text-base font-semibold text-secondary">{{ $lcm }}/{{ $lpm }}</span>
                            </label>

                            <progress class="progress progress-success w-[90%] mx-auto" value="{{ $ctm * 100 }}"
                                max="100"></progress>
                        </div>

                        <div
                            class="flex flex-col space-y-1 bg-base-200 justify-center h-16 w-full lg:w-1/4 rounded-xl items-center py-4">
                            <label
                                class=" font-medium text-primary w-[90%] flex justify-between items-center">
                                <span>Total scheduled follow ups pending</span>
                                <span class="text-lg font-semibold text-secondary">{{ $pf }}</span>
                            </label>

                        </div>

                    </div>


                    <div class="flex flex-col md:flex-row md:flex-wrap space-x-2">

                    {{-- Chart Canvas --}}

                        <div class="w-80 p-2 aspect-square rounded-xl bg-base-200 h-fit mt-5">
                            <canvas id="chartCanvas"></canvas>
                        </div>

                        <div class="w-80 p-2 aspect-square rounded-xl bg-base-200 h-fit mt-5">
                            <canvas id="validChartCanvas"></canvas>
                        </div>

                        <div class="w-80 p-2 aspect-square rounded-xl bg-base-200 h-fit mt-5">
                            <canvas id="genuineChartCanvas"></canvas>
                        </div>

                    </div>
                    <div class=" flex flex-col space-y-5 ">

                        {{-- @can('is-admin') --}}
                        <div>
                            <h1 class=" font-medium text-base-content lg:text-lg">Total consulted count for the period</h1>
                            <div class="rounded-lg w-fit overflow-hidden border border-opacity-60 h-fit">

                                <div class="overflow-x-auto">

                                    <table class="table">
                                      <!-- head -->
                                        <thead>
                                            <tr class=" bg-base-300 text-secondary">
                                            <th>
                                                @if (auth()->user()->hasRole('admin'))
                                                    Agent
                                                    @else
                                                    Name
                                                @endif
                                            </th>
                                            <th>Total Consulted</th>
                                            </tr>
                                        </thead>
                                        <tbody class=" text-base-content font-medium text-sm h-fit">
                                            @foreach ($totalConsulted as $k => $d)
                                                @if($k != 'Total')
                                                <tr class="bg-base-200">
                                                    <td class="text-left">{{$agents[$k] ?? '0'}}</td>
                                                    <td class=" text-center">
                                                        {{-- <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all'
                                                        ])}}" target="blank" class="text-warning hover:underline"> --}}
                                                            {{$d}}
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                            <tr class="bg-base-300 font-bold">
                                                <td>Total</td>
                                                <td class="text-center">{{$totalConsulted['Total']}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div>
                            <h1 class=" font-medium text-base-content lg:text-lg">Agent Analysis</h1>
                            <div class="rounded-lg w-fit overflow-hidden border border-opacity-60 h-fit">
                                <div class="overflow-x-auto">

                                    <table class="table">
                                      <!-- head -->
                                      <thead>
                                        <tr class=" bg-base-300 text-secondary">
                                          <th class="w-36">
                                            @if (auth()->user()->hasRole('admin'))
                                                Agent
                                                @else
                                                Name
                                            @endif
                                          </th>
                                          <th>Total Leads</th>
                                          <th>Followup initiated leads</th>
                                          <th>Valid leads</th>
                                          <th>Genuine leads</th>
                                          <th>Hot</th>
                                          <th>Warm</th>
                                          <th>Cold</th>
                                          <th>Consulted/Completed leads</th>
                                          <th>Closed leads</th>
                                          <th>Responsive leads</th>
                                          <th>Non responsive leads</th>
                                          {{-- <th></th> --}}
                                        </tr>
                                      </thead>
                                      <tbody class=" text-base-content font-medium text-sm h-fit">
                                        @foreach ($agentsReport as $k => $d)
                                            @if ((auth()->user()->id == $k || auth()->user()->hasRole('admin')) && $k != 'Total')
                                                <tr class="bg-base-200 hover:bg-base-100">
                                                    <th class="">{{$agents[$k] ?? '0'}}</th>
                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all'
                                                        ])}}" target="blank" class="text-warning hover:underline">
                                                            {{$d['total_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'agent' => $k,
                                                        'status' => 'Follow-up Started',
                                                    ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                        {{$d['followup_initiated_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'is_valid' => 'true'
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['valid_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'is_genuine' => 'true'
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['genuine_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'segment' => 'hot'
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['hot_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'segment' => 'warm'
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['warm_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'segment' => 'cold'
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['cold_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'At Least Consulted',
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['consulted_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'Closed',
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['closed_leads'] ?? '0'}}
                                                        </a>
                                                    </td>

                                                    <td class=" text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'call_status' => 'Responsive',
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['followup_initiated_leads'] - $d['non_responsive_leads']}}
                                                        </a>

                                                    </td>

                                                    <td class="text-center">
                                                        <a href="{{route('fresh-leads',[
                                                            'creation_date_from' => $from,
                                                            'creation_date_to' => $to,
                                                            'agent' => $k,
                                                            'status' => 'all',
                                                            'call_status' => 'Not responsive',
                                                        ])}}" target="blank" class="text-warning hover:text-blue-600 hover:underline">{{$d['non_responsive_leads'] ?? '0'}}
                                                        </a>

                                                    </td>
                                                    {{-- <td><button class="btn btn-xs btn-ghost text-primary lowercase">view</button></td> --}}
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr class="bg-base-300 font-bold">
                                            <td>Total</td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['total_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['followup_initiated_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['valid_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['genuine_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['hot_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['warm_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['cold_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['consulted_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['closed_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['followup_initiated_leads'] - $agentsReport['Total']['non_responsive_leads']}}
                                            </td>
                                            <td class="text-center">
                                                {{$agentsReport['Total']['non_responsive_leads']}}
                                            </td>
                                        </tr>

                                        @if (count($counts) < 1)
                                                <tr>
                                                    <td>No data for the selected month</td>
                                                </tr>
                                        @endif

                                      </tbody>
                                    </table>
                                  </div>
                            </div>
                        </div>


                        <div>
                        <h1 class=" font-medium text-base-content lg:text-lg">Follow-up Analysis</h1>
                        <div class="rounded-lg w-fit overflow-hidden border border-opacity-60 h-fit">
                            <div class="overflow-x-auto">

                                <table class="table">
                                  <!-- head -->
                                  <thead>
                                    <tr class=" bg-base-300 text-secondary">
                                      <th>
                                        @if (auth()->user()->hasRole('admin'))
                                            Agent
                                            @else
                                            Name
                                        @endif
                                      </th>
                                      <th>Total Leads</th>
                                      <th>Responsive Leads</th>
                                      <th>Followup initiated leads</th>
                                      <th>Total follow-ups</th>
                                      <th>Responsive follow-ups</th>
                                      <th>Non responsive follow-ups</th>
                                      <th>Scheduled follow-ups</th>
                                      {{-- <th></th> --}}
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm h-fit">

                                    @foreach ($counts as $k => $d)
                                        @if ((auth()->user()->id == $k || auth()->user()->hasRole('admin')) && $k != 'Total')
                                            <tr class="bg-base-200 hover:bg-base-100">
                                                <th class=" text-center">
                                                    {{$agents[$k] ?? '0'}}
                                                </th>

                                                <td class=" text-center">
                                                    <a href="{{route('fresh-leads',[
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'agent' => $k,
                                                        'status' => 'all'
                                                    ])}}" target="blank" class="text-warning hover:underline">
                                                        {{$d['lpm'] ?? '0'}}
                                                    </a>
                                                </td>

                                                <td class=" text-center">
                                                    <a href="{{route('fresh-leads',[
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'agent' => $k,
                                                        'status' => 'all',
                                                        'call_status' => 'Responsive'
                                                    ])}}" target="blank" class="text-warning hover:underline">
                                                    {{$d['responsive_leads'] ?? '0'}}
                                                    </a>
                                                </td>

                                                <td class=" text-center">
                                                    <a href="{{route('fresh-leads',[
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'agent' => $k,
                                                        'status' => 'At Least Follow-up Started',
                                                        'call_status' => 'Responsive'
                                                    ])}}" target="blank" class="text-warning hover:underline">
                                                    {{$d['followup_initiated_leads'] ?? '0'}}
                                                    </a>
                                                </td>

                                                <td class=" text-center">
                                                    {{$d['ftm'] ?? '0'}}
                                                </td>

                                                <td class=" text-center">
                                                    {{$d['responsive_followups'] ?? '0'}}
                                                </td>

                                                <td class=" text-center">
                                                    {{$d['non_responsive_followups'] ?? '0'}}
                                                </td>

                                                <td class=" text-center">
                                                    {{$d['pf'] ?? '0'}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                        <tr class="bg-base-300 font-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{$counts['Total']['lpm']}}</td>
                                            <td class="text-center">{{$counts['Total']['responsive_leads']}}</td>
                                            <td class="text-center">{{$counts['Total']['followup_initiated_leads']}}</td>
                                            <td class="text-center">{{$counts['Total']['ftm']}}</td>
                                            <td class="text-center">{{$counts['Total']['responsive_followups']}}</td>
                                            <td class="text-center">{{$counts['Total']['non_responsive_followups']}}</td>
                                            <td class="text-center">{{$counts['Total']['pf']}}</td>
                                        </tr>
                                    @if (count($counts) < 1)
                                            <tr>
                                                <td>No data for the selected month</td>
                                            </tr>
                                    @endif

                                  </tbody>
                                </table>
                              </div>
                        </div>
                    </div>

                        {{-- @endcan --}}
                        <div>
                        <h1 class=" font-medium text-base-content lg:text-lg">Campaign Analysis</h1>
                        <div class="rounded-lg w-fit overflow-hidden border border-opacity-60">
                            <div class="overflow-x-auto">
                                <table class="table">
                                  <!-- head -->
                                  <thead>
                                    <tr class=" bg-base-300 text-secondary">
                                      <th>Campaign</th>
                                      <th>Total Leads</th>
                                      <th>Follow-up initiated leads</th>
                                      <th>Valid leads</th>
                                      <th>Genuine leads</th>
                                      <th>Hot</th>
                                      <th>Warm</th>
                                      <th>Cold</th>
                                      <th>Leads Consulted/Completed</th>
                                      <th>Closed leads</th>
                                      <th>Non responsive leads</th>
                                      {{-- <th></th> --}}
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm">


                                    @foreach ($campaignReport as $campaign => $data)
                                        @if($campaign != 'Total')
                                        <tr class="bg-base-200 hover:bg-base-100">
                                            <th class=" text-center">
                                                {{$campaign == "" ? 'Direct leads' : $campaign}}
                                            </th>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['total_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'Follow-up Started',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                    {{$data['followup_initiated_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'is_valid' => 'true',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['valid_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'is_genuine' => 'true',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['genuine_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'hot',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['hot_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'warm',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['warm_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'cold',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['cold_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'At Least Consulted',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['leads_converted'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'Closed',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                    {{$data['closed_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'status' => 'all',
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'call_status' => 'Not Responsive',
                                                        'campaign' => $campaign
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['non_responsive_leads'] ?? '0'}}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                        <tr class="bg-base-300 font-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{$campaignReport['Total']['total_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['followup_initiated_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['valid_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['genuine_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['hot_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['warm_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['cold_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['leads_converted']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['closed_leads']}}</td>
                                            <td class="text-center">{{$campaignReport['Total']['non_responsive_leads']}}</td>
                                        </tr>
                                    @if (count($campaignReport) < 1)
                                            <tr>
                                                <td>No data for the selected month</td>
                                            </tr>
                                    @endif

                                  </tbody>
                                </table>
                              </div>
                        </div>
                    </div>


                        {{-- Source report --}}
                        <div>
                        <h1 class=" font-medium text-base-content lg:text-lg">Source Analysis</h1>
                        <div class="rounded-lg w-fit overflow-hidden border border-opacity-60">
                            <div class="overflow-x-auto">
                                <table class="table">
                                  <!-- head -->
                                  <thead>
                                    <tr class=" bg-base-300 text-secondary">
                                      <th>Source</th>
                                      <th>Total leads</th>
                                      <th>Follow-up initiated leads</th>
                                      <th>Valid leads</th>
                                      <th>Genuine leads</th>
                                      <th>Hot</th>
                                      <th>Warm</th>
                                      <th>Cold</th>
                                      <th>Leads Consulted/Completed</th>
                                      <th>Closed leads</th>
                                      <th>Non responsive leads</th>
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm">


                                    @foreach ($sourceReport as $source => $data)
                                        @if ($source != 'Total')
                                        <tr class="bg-base-200 hover:bg-base-100">
                                            <th class=" text-center">
                                                {{$source}}
                                            </th>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['total_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'At Least Follow-up Started',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['followup_initiated_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'is_valid' => 'true',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['valid_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'is_genuine' => 'true',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                    {{$data['genuine_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'hot',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['hot_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'warm',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['warm_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'segment' => 'cold',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['cold_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'At Least Consulted',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['converted_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'Closed',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                {{$data['closed_leads'] ?? '0'}}
                                                </a>
                                            </td>

                                            <td class=" text-center">
                                                <a href="{{
                                                route(
                                                    'fresh-leads',
                                                    [
                                                        'creation_date_from' => $from,
                                                        'creation_date_to' => $to,
                                                        'status' => 'all',
                                                        'call_status' => 'Not responsive',
                                                        'source' => $data['source_id']
                                                    ]
                                                )
                                                }}" target="blank" class="text-warning hover:text-blue-600 hover:underline">
                                                    {{$data['non_responsive_leads'] ?? '0'}}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    <tr class="bg-base-300 font-bold">
                                        <td>Total</td>
                                        <td class="text-center">{{$sourceReport['Total']['total_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['followup_initiated_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['valid_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['genuine_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['hot_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['warm_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['cold_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['converted_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['closed_leads']}}</td>
                                        <td class="text-center">{{$sourceReport['Total']['non_responsive_leads']}}</td>
                                    </tr>
                                    @if (count($sourceReport) < 1)
                                            <tr>
                                                <td>No data for the selected month</td>
                                            </tr>
                                    @endif

                                  </tbody>
                                </table>
                              </div>
                        </div>
                    </div>


                    </div>


                </div>


            </div>

        </div>
    </div>
    <x-footer />
</x-easyadmin::app-layout>
