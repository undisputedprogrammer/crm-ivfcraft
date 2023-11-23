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
    @isset($from)
        console.log('{{$from}}');
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
                                    <label for="" class=" text-primary font-medium">From :</label>
                                    <input type="date" name="from" required value="{{$from}}" class=" input input-sm input-bordered border-primary">
                                </div>
                                <div class=" flex flex-col">
                                    <label for="" class=" text-primary font-medium">To :</label>
                                    <input type="date" name="to" required value="{{$to}}" class=" input input-sm input-bordered border-primary">
                                </div>

                                <button type="submit" class=" btn btn-sm btn-primary h-fit self-end">Search</button>

                                <button @click.prevent.stop="resetPerformancePage()" class=" btn btn-sm btn-ghost h-fit self-end">
                                    <x-icons.refresh-icon/>
                                </button>
                            </div>
                        </form>

                    </div>

                    <div
                        class="flex flex-col space-y-2 md:space-y-0 md:flex-row  md:space-x-3 justify-evenly md:items-center ">

                        <div
                            class="flex flex-col space-y-1 bg-base-200 w-full lg:w-1/4 h-16 rounded-xl justify-center items-center py-4">
                            <label for=""
                                class=" font-medium text-primary w-[90%] flex justify-between items-center">
                                <span>Total leads this month</span>
                                <span class="text-lg font-semibold text-secondary">{{ $lpm }}</span>
                            </label>
                            {{-- <progress class="progress progress-success w-[90%] mx-auto" value="50" max="100"></progress> --}}
                        </div>

                        <div class="flex flex-col space-y-1 bg-base-200 w-full lg:w-1/4 rounded-xl items-center py-4">
                            <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
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
                            <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
                                <span>Leads converted this month</span>
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
                            <label for=""
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
                                      <th>Leads Consulted</th>
                                      <th>Scheduled follow-ups</th>
                                      {{-- <th></th> --}}
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm h-fit">

                                    @foreach ($counts as $k => $d)
                                        @if (auth()->user()->id == $k || auth()->user()->hasRole('admin'))
                                            <tr class="bg-base-200 hover:bg-base-100">
                                                <th>{{$agents[$k] ?? '0'}}</th>
                                                <td>{{$d['lpm'] ?? '0'}}</td>
                                                <td>{{$d['responsive_leads'] ?? '0'}}</td>
                                                <td>{{$d['followup_initiated_leads'] ?? '0'}}</td>
                                                <td>{{$d['ftm'] ?? '0'}}</td>
                                                <td>{{$d['responsive_followups'] ?? '0'}}</td>
                                                <td>{{$d['non_responsive_followups'] ?? '0'}}</td>
                                                <td>{{$d['lcm'] ?? '0'}}</td>
                                                <td>{{$d['pf'] ?? '0'}}</td>
                                                {{-- <td><button class="btn btn-xs btn-ghost text-primary lowercase">view</button></td> --}}
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if (count($counts) < 1)
                                            <tr>
                                                <td>No data for the selected month</td>
                                            </tr>
                                    @endif

                                  </tbody>
                                </table>
                              </div>
                        </div>

                        {{-- @endcan --}}

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
                                      <th>Leads Consulted</th>
                                      <th>Closed leads</th>
                                      <th>Non responsive leads</th>
                                      {{-- <th></th> --}}
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm">


                                    @foreach ($campaignReport as $campaign => $data)
                                        <tr class="bg-base-200 hover:bg-base-100">
                                            <th>{{$campaign == "" ? 'Direct leads' : $campaign}}</th>
                                            <td>{{$data['total_leads'] ?? '0'}}</td>
                                            <td>{{$data['followup_initiated_leads'] ?? '0'}}</td>
                                            <td>{{$data['valid_leads'] ?? '0'}}</td>
                                            <td>{{$data['genuine_leads'] ?? '0'}}</td>
                                            <td>{{$data['hot_leads'] ?? '0'}}</td>
                                            <td>{{$data['warm_leads'] ?? '0'}}</td>
                                            <td>{{$data['cold_leads'] ?? '0'}}</td>
                                            <td>{{$data['leads_converted'] ?? '0'}}</td>
                                            <td>{{$data['closed_leads'] ?? '0'}}</td>
                                            <td>{{$data['non_responsive_leads'] ?? '0'}}</td>
                                        </tr>
                                    @endforeach
                                    @if (count($campaignReport) < 1)
                                            <tr>
                                                <td>No data for the selected month</td>
                                            </tr>
                                    @endif

                                  </tbody>
                                </table>
                              </div>
                        </div>


                        {{-- Source report --}}

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
                                      <th>Leads Consulted</th>
                                      <th>Closed leads</th>
                                      <th>Non responsive leads</th>
                                    </tr>
                                  </thead>
                                  <tbody class=" text-base-content font-medium text-sm">


                                    @foreach ($sourceReport as $source => $data)
                                        <tr class="bg-base-200 hover:bg-base-100">
                                            <th>{{$source}}</th>
                                            <td>{{$data['total_leads'] ?? '0'}}</td>
                                            <td>{{$data['followup_initiated_leads'] ?? '0'}}</td>
                                            <td>{{$data['valid_leads'] ?? '0'}}</td>
                                            <td>{{$data['genuine_leads'] ?? '0'}}</td>
                                            <td>{{$data['hot_leads'] ?? '0'}}</td>
                                            <td>{{$data['warm_leads'] ?? '0'}}</td>
                                            <td>{{$data['cold_leads'] ?? '0'}}</td>
                                            <td>{{$data['converted_leads'] ?? '0'}}</td>
                                            <td>{{$data['closed_leads'] ?? '0'}}</td>
                                            <td>{{$data['non_responsive_leads'] ?? '0'}}</td>
                                        </tr>
                                    @endforeach
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
    <x-footer />
</x-easyadmin::app-layout>
