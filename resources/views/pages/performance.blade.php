<x-easyadmin::app-layout>
    {{-- {{dd($agents)}} --}}
    {{-- @foreach ($counts as $k => $d)
        <div>
            {{$agents[$k]}}:
            @foreach ($d as $key => $val)
                <span>{{$key}} : {{$val}}</span>
            @endforeach
        </div>
    @endforeach --}}

    <div x-data="x_overview"
    x-init = "@if(isset($journal))
    journal = {{$journal}};
    @endif
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
        console.log(genuineChartData);
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
                            <h1 class=" font-medium uppercase">Choose month</h1>
                            <div class=" flex space-x-4">
                                <input type="month" name="month" class=" input input-sm input-bordered border-primary">
                                <button type="submit" class=" btn btn-sm btn-primary">Search</button>
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

                    <div class="rounded-lg w-fit overflow-hidden border border-opacity-60">
                        <div class="overflow-x-auto">
                            <table class="table">
                              <!-- head -->
                              <thead>
                                <tr class=" bg-base-300 text-secondary">
                                  <th>Agent</th>
                                  <th>Total Leads</th>
                                  <th>Follow-ups</th>
                                  <th>Converted</th>
                                  <th>Pending Follow-ups</th>
                                  {{-- <th></th> --}}
                                </tr>
                              </thead>
                              <tbody class=" text-base-content font-medium text-sm">
                                @foreach ($counts as $k => $d)
                                    <tr class="bg-base-200 hover:bg-base-100">
                                        <th>{{$agents[$k] ?? '0'}}</th>
                                        <td>{{$d['lpm'] ?? '0'}}</td>
                                        <td>{{$d['ftm'] ?? '0'}}</td>
                                        <td>{{$d['lcm'] ?? '0'}}</td>
                                        <td>{{$d['pf'] ?? '0'}}</td>
                                        {{-- <td><button class="btn btn-xs btn-ghost text-primary lowercase">view</button></td> --}}
                                    </tr>
                                @endforeach

                              </tbody>
                            </table>
                          </div>
                    </div>

                </div>


            </div>

        </div>
    </div>
    <x-footer />
</x-easyadmin::app-layout>
