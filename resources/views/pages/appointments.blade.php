<x-easyadmin::app-layout>
<div x-data="x_leads" x-init="
    selectedCenter = null;
    @isset($selectedCenter)
        selectedCenter = '{{$selectedCenter}}';
    @endisset">
    <div
        x-data="{
            from: '',
            to: '',
            center: null,
            doSubmit() {
                $dispatch('linkaction', {link: '{{route('appointments.index')}}',
                route: 'appointments.index', fresh: true, fragment: 'page-content', params: {from: this.from, to: this.to, center: this.center}});
            }
        }"
        x-init="
            @if (isset(request()->from))
                from = '{{request()->from}}';
            @endif

            @if (isset(request()->to))
                to = '{{request()->to}}';
            @endif

            @if (isset(request()->center))
                center = '{{request()->center}}';
            @endif
        "
        class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
        <h2 class=" text-lg font-semibold text-primary bg-base-200">Manage Appointments</h2>
      </div>

        {{-- Appointment search form --}}
        <form id="appointments-search-form"
            action=""
            @submit.prevent.stop="doSubmit();"
            class="flex flex-col md:flex-row w-full m-auto justify-center space-y-3 md:space-y-0 md:space-x-8 bg-base-200 md:items-end items-start px-4">
            <div class="form-control max-w-xs flex flex-row md:flex-col">
                <label class="label w-12">
                  <span class="label-text">From</span>
                </label>
                <input x-model="from" name="from" type="date" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" />
            </div>
            <div class="form-control max-w-xs flex flex-row md:flex-col">
                <label class="label w-12">
                  <span class="label-text">To</span>
                </label>
                <input x-model="to" name="to" type="date" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" />
            </div>
            {{-- Select center, optional --}}
            <div class="form-control max-w-xs flex flex-row md:flex-col">
                <label class="label w-12">
                  <span class="label-text">Center</span>
                </label>
                <select x-model="center" name="center" id="select_center" class="select bg-base-100 text-base-content">
                    <option :selected="selectedCenter == null || selectedCenter != 'all' " value="all">All centers</option>
                    @foreach ($centers as $center)
                        <option :selected="selectedCenter == '{{$center->id}}' " value="{{$center->id}}">{{$center->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-row ">
                <label for="" class="w-12 md:hidden"></label>
                <button type="submit" class="btn btn-md btn-secondary">Search</button>
            </div>
        </form>
      <div x-data="{page: 0}"
        x-init="
            page = {{request()->input('page', 0)}};
        "

        {{-- pagination event handler --}}
        @pageaction.window="
            page = $event.detail.page;
            let params = {
                page: page
            };
            if (from != '') {
                params.from = from;
            }
            if (to != '') {
                params.to = to;
            }
            $dispatch('linkaction',{
                link: '{{route('appointments.index')}}',
                route: currentroute,
                fragment: 'page-content',
                params: params
            })"

       class=" lg:h-[calc(100vh-3.5rem)] pt-7 pb-12  bg-base-200 w-full flex flex-col lg:flex-row justify-evenly space-y-4 lg:space-y-0 items-center lg:items-start ">


        <x-tables.appointments-table :appointments="$appointments"/>



        <div id="appointment-details"
            x-data="{
                appointments: [],
                appointment: [],
                doctor: [],
                lead: [],
                leadremarks: [],
                contentRecieved: false
            }"
            @dataupdate.window="
                if($event.detail.target == $el.id){
                    console.log($event.detail.appointment);
                    appointment = $event.detail.appointment;
                    appointments[appointment.id] = appointment;
                    lead = appointment.lead;
                    leadremarks = lead.remarks;
                    doctor = appointment.doctor;
                    contentRecieved = true;
                }
            "
            class=" w-[96%] lg:w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">

            <p x-show="!contentRecieved" x-transition class=" text-lg font-medium text-base-content text-center">Select an appointment</p>

            <div x-show="contentRecieved" x-transition x-cloak>
                <h2 class=" text-secondary font-semibold text-lg mb-2">Appointment Details</h2>
                <p class="font-medium text-base-content text-base">Prospect name : <span x-text="lead.name" class=" text-primary"></span></p>
                <p class="font-medium text-base-content text-base">Doctor name : <span x-text="doctor.name" class=" text-primary"></span></p>
                <p class="font-medium text-base-content text-base">Appointment date : <span x-text="appointment.appointment_date" class=" text-primary"></span></p>

                <h1 class=" text-base font-semibold text-secondary mt-3">More details on lead</h1>
                <p class="text-base font-medium">City : <span x-text="lead != undefined ? lead.city : '' "> </span></p>
                <p class="text-base font-medium">Phone : <span x-text=" lead != undefined ? lead.phone : '' "> </span></p>
                <p class="text-base font-medium">Email : <span x-text=" lead != undefined ? lead.email : '' "> </span></p>
                <p class="text-base font-medium ">Status : <span class="uppercase" x-text=" lead != undefined ? lead.status : '' " :class=" lead.status == 'Closed' ? ' text-error' : ' text-success' " > </span></p>

                <div class="mt-2.5">
                    <p class=" text-base font-medium text-secondary">Lead remarks</p>

                    <ul class=" list-disc text-sm list-outside font-normal">
                        <template x-for="remark in leadremarks">

                            <li x-text="remark.remark"></li>

                        </template>
                    </ul>
                </div>

            </div>

        </div>



      </div>
    </div>
  </div>
<x-footer/>
</x-easyadmin::app-layout>
