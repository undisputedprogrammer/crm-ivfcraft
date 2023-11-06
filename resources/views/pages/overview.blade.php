<x-easyadmin::app-layout>
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
                    <h1 class=" text-xl font-semibold text-primary ">Overview</h1>

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

                    {{-- Chart Canvas --}}
                    <div class="flex flex-row justify-evenly">
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
                    @can('is-admin')
                        <div class="bg-base-200 rounded-xl  p-3 w-fit m-auto">
                            <h2 class="text-primary font-medium mb-2.5">Actions</h2>

                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 flex-wrap sm:space-x-2 mt-1">

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('appointments.index') }}',
                                        route:'appointments.index',
                                        fragment:'page-content'
                                    })">Manage
                                    Appointments
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('doctors.index') }}',
                                        route:'doctors.index',
                                        fragment:'page-content'
                                    })">Manage
                                    Doctors
                                </button>

                                <button class="btn btn-sm btn-secondary "
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('agents.index') }}',
                                        route:'agents.index',
                                        fragment:'page-content'
                                    })">Manage
                                    Agents
                                </button>

                                <button class="btn btn-sm btn-secondary "
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('leads.reassign') }}',
                                        route:'leads.reassign',
                                        fragment:'page-content'
                                    })">Re-assign
                                    Leads
                                </button>

                                <button class="btn btn-sm btn-secondary "
                                    @click.prevent.stop="$dispatch('linkaction',{
                                        link:'{{ route('performance') }}',
                                        route:'performance',
                                        fragment:'page-content'
                                    })">Performance Analysis
                                </button>

                            </div>
                        </div>
                    @endcan
                    {{-- <div class="flex flex-col md:flex-row md:flex-wrap space-x-2"> --}}
                    {{-- import leads form --}}
                    <div class="flex flex-row justify-evenly">
                        @can('import-lead')
                        <div class=" bg-base-200 p-3 rounded-xl w-fit">
                            <h1 class="font-semibold mb-2.5 text-primary text-center">Import leads</h1>
                            <form
                                x-data="{
                                    fileName: '',
                                    hospital: '',
                                    center: '',
                                    hospitals: [],
                                    centers: [],
                                    agents: [],
                                    allAgents: [],
                                    fetchCenters() {
                                        axios.get(
                                            '{{route('hospital.centers')}}',
                                            {
                                                params: { 'hospital': this.hospital }
                                            }
                                        ).then(
                                            (r) => {
                                                this.centers = r.data.centers;
                                            }
                                        ).catch(
                                            (e) => {
                                                console.log(e);
                                            }
                                        );
                                    },
                                    isDisabled() {
                                        return this.fileName == ''
                                            || this.hospital == ''
                                            || this.center =='';
                                    },
                                    fetchAgents() {
                                        axios.get(
                                            '{{route('center.agents')}}',
                                            {
                                                params: { 'cid': this.center }
                                            }
                                        ).then((r) => {
                                            let data = r;
                                            this.allAgents = data.data.agents;
                                        }).catch((e) => {
                                            console.log(e);
                                        });
                                    },
                                    doSubmit() {
                                        let form = document.getElementById('import-form');
                                        let formdata = new FormData(form);
                                        $dispatch('formsubmit', { url: '{{ route('import-leads') }}', route: 'import-leads', fragment: 'page-content', formData: formdata, target: 'import-form' });
                                        form.reset();
                                        this.fileName = '';
                                    }
                                }"
                                x-init="
                                    hospitals={{Js::from($hospitals)}};
                                    hospital = hospitals[0];
                                    centers = {{Js::from($centers)}};
                                    $center = centers[0];
                                    $watch('hospital', (h) => {
                                        center = '';
                                        fetchCenters();
                                    });
                                    $watch('center', (c) => {
                                            fetchAgents();
                                        }
                                    );
                                "
                                @submit.prevent.stop="doSubmit();"
                                @formresponse.window="
                                if ($event.detail.target == $el.id) {
                                    if ($event.detail.content.success) {
                                            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});

                                            $dispatch('formerrors', {errors: []});
                                            $dispatch('linkaction', {
                                                link: '{{route('overview')}}',
                                                route: 'overview',
                                                fresh: true,
                                                fragment: 'page-content'
                                            })
                                        } else if (typeof $event.detail.content.errors != undefined) {
                                            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                                        } else{
                                            $dispatch('formerrors', {errors: $event.detail.content.errors});
                                        }
                                }"
                                id="import-form" class="flex flex-col space-y-3 items-center"
                                >
                                <input type="file" name="sheet" @change="fileName = $el.files[0].name"
                                    class="file-input file-input-bordered file-input-success text-base-content file-input-sm w-full max-w-xs" accept=".xlsx" />

                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">Hospital</span>
                                    </label>
                                    <select name="hospital" x-model="hospital" class="select select-bordered text-base-content">
                                        <option disabled value="">Pick one</option>
                                        <template x-for="h in hospitals">
                                        <option :value="h.id" x-text="h.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">Center</span>
                                    </label>
                                    <select name="center" x-model="center" class="select select-bordered text-base-content">
                                        <option disabled value="">Pick one</option>
                                        <template x-for="c in centers">
                                        <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">Agents</span>
                                    </label>
                                    <select name="agents[]" x-model="agents" multiple class="select select-bordered text-base-content">
                                        <option disabled value="">Choose Agents</option>
                                        <template x-for="a in allAgents">
                                        <option :value="a.id" x-text="a.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-sm btn-success normal-case" :disabled="isDisabled()">Import</button>
                                <div>
                                    <button class="btn btn-sm btn-ghost text-base-content opacity-60 normal-case" type="reset">Cancel</button>
                                </div>
                            </form>
                        </div>
                        @endcan

                        @can('is-agent')
                        <div class="flex flex-col space-y-8">
                            <div class="bg-base-200 w-full p-3 rounded-lg border border-secondary text-base-content">
                                <h2 class=" font-mono font-semibold text-base">Daily Journel</h2>
                                <p class="font-bold text-secondary" x-text="getDate();"></p>
                                <p style="white-space: pre-line;" class="text-sm my-2 font-medium" x-html="journal != null ? escapeSingleQuotes(journal.body) : 'Enter today\'s journal' " ></p>

                                <form @submit.prevent.stop="journalSubmit($el.id,'{{route('journal.store')}}', 'journal.store');" id="add-journal-form" action="" class=" mt-3"
                                @formresponse.window="
                                if($el.id == $event.detail.target){
                                    postJournalSubmission($event.detail.content);
                                    $el.reset();
                                }">
                                    <textarea name="body" id="journal-body"
                                    class=" textarea textarea-ghost min-w-72 lg:w-full bg-base-100 focus-within:border-secondary focus:outline-none"
                                    placeholder="Enter today's report"></textarea>

                                    <button type="submit" class="btn btn-sm btn-primary mt-1.5">Save</button>
                                </form>
                                <div class="text-center">
                                    <a class="btn btn-link inline-block my-8 normal-case" href=""
                                        @click.prevent.stop="
                                            $dispatch('linkaction',
                                            {
                                                link: '{{route('journals.fetch_own')}}',
                                                route: 'journals.fetch_own',
                                                fresh: true
                                            })
                                        ">
                                        View Journals
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>


            </div>

        </div>
    </div>
    <x-footer />
</x-easyadmin::app-layout>
