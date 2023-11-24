<x-easyadmin::app-layout>

<div x-data="x_followups"
        x-init="
        theLink='{{route('followups')}}';
        selectedCenter = null;
        @isset($selectedCenter)
            selectedCenter = {{$selectedCenter}};
        @endisset
        @isset($selectedAgent)
            selectedAgent = {{$selectedAgent}};g
        @endisset
        page = getPage();
        @isset($status)
        selectedStatus = '{{$status}}';
        @endisset
        @isset($is_valid)
            is_valid = '{{$is_valid}}';
        @endisset
        @isset($is_genuine)
            is_genuine = '{{$is_genuine}}';
        @endisset
        @isset($creation_date_from)
            creation_date_from = '{{$creation_date_from}}';
        @endisset
        @isset($creation_date_to)
            creation_date_to = '{{$creation_date_to}}';
        @endisset
        @isset($segment)
            segment = '{{$segment}}';
        @endisset
        @isset($campaign)
            campaign = '{{$campaign}}';
        @endisset
        @isset($source)
            source = '{{$source}}';
        @endisset
        @isset($search)
            search = '{{$search}}';
        @endisset"

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('linkaction',{
            link: $event.detail.link,
            route: currentroute,
            fragment: 'page-content',
        })"

        >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200 px-[1.7%]">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div class=" flex flex-col justify-start items-start w-full bg-base-200 pt-1.5  space-y-2">
        <h1 class=" text-primary text-xl font-semibold bg-base-200 ">Pending follow ups</h1>

        <div class=" flex flex-row space-x-4 border border-base-content rounded-lg p-2">
            <form @submit.prevent.stop="filterLead($el,'{{route('followups')}}');" class=" flex flex-col  space-y-2">

                <div class=" flex flex-col md:flex-row flex-wrap ">

                    @can('is-admin')
                        <div class=" flex flex-col ml-3 mb-1.5">
                            <label for="" class=" text-primary font-medium text-xs">Center :</label>
                            <select name="center" id="select-center" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                                <option value="all">All Centers</option>
                                @foreach ($centers as $center)
                                    <option value="{{$center->id}}" :selected="selectedCenter == '{{$center->id}}' ">{{$center->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class=" flex flex-col ml-3 mb-1.5">
                            <label for="" class=" text-primary font-medium text-xs">Agent :</label>
                            <select name="agent" id="select-agent" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                                <option value="all">All Agents</option>
                                @foreach ($agents as $agent)
                                    <option value="{{$agent->id}}" :selected="selectedAgent == '{{$agent->id}}' ">{{$agent->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endcan


                    <div class=" flex flex-col ml-3 mb-1.5">
                        <label for="" class=" text-xs text-primary font-medium">Status :</label>
                        <select name="status" id="select-status" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="all" :selected="'{{$status}}'=='all' ">All leads</option>
                            @foreach (config('appSettings')['lead_statuses'] as $st)
                            <template x-if="'{{$st}}' != 'Created'">
                                <option value="{{$st}}" :selected="'{{$status}}' == '{{$st}}' ">{{$st}}</option>
                            </template>
                            @endforeach
                        </select>
                    </div>

                    <div class=" flex-col flex ml-3 mb-1.5">

                        <label for="" class=" text-xs text-primary font-medium">Segment : </label>
                        <select name="segment" id="segment" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="">Not Selected</option>
                            @foreach (config('appSettings.lead_segments') as $segment)
                                <option :selected="segment == '{{$segment}}'" value="{{$segment}}">{{ucfirst($segment)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" flex-col flex ml-3 mb-1.5">

                        <label for="" class=" text-xs text-primary font-medium">Campaign : </label>
                        <select name="campaign" id="campaign" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="">Not Selected</option>
                            @foreach ($campaigns as $campaign)
                                <option :selected="campaign == '{{$campaign->name}}'" value="{{$campaign->name}}">{{ucfirst($campaign->name)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" flex-col flex ml-3 mb-1.5">

                        <label for="" class=" text-xs text-primary font-medium">Sources : </label>
                        <select name="source" id="source" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="">Not Selected</option>
                            @foreach ($sources as $source)
                                <option :selected="source == '{{$source->id}}'" value="{{$source->id}}">{{ucfirst($source->name)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" flex-col flex ml-3 mb-1.5">

                        <label for="" class=" text-xs text-primary font-medium">Validity :</label>
                        <select name="is_valid" id="is-valid" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="">Not Selected</option>
                            <option :selected="is_valid == 'true'" value="true">Valid</option>
                            <option :selected="is_valid == 'false'" value="false">Not Valid</option>
                        </select>
                    </div>

                    <div class=" flex flex-col ml-3 mb-1.5">
                        <label for="" class=" text-xs text-primary font-medium">Genuinity :</label>
                        <select name="is_genuine" id="is-genuine" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
                            <option value="">Not Selected</option>
                            <option :selected="is_genuine == 'true'" value="true">Genuine</option>
                            <option :selected="is_genuine == 'false'" value="false">Not Genuine</option>
                        </select>
                    </div>

                    <div class=" flex flex-col ml-3 mb-1.5">
                        <label for="" class=" text-xs text-primary font-medium">Created from :</label>
                        <input type="date" :value="creation_date_from != null ? creation_date_from : null" name="creation_date_from" class=" input input-sm text-base-content font-medium">
                    </div>

                    <div class=" flex flex-col ml-3 mb-1.5">
                        <label for="" class=" text-xs text-primary font-medium">Created to :</label>
                        <input type="date" :value="creation_date_to != null ? creation_date_to : null" name="creation_date_to" class=" input input-sm text-base-content font-medium">
                    </div>


                    <button type="submit" class=" btn-primary btn btn-sm self-end ml-3 mb-1.5">Filter</button>

                    <div class=" self-end flex flex-row bg-base-100 border border-primary px-1 rounded-lg ml-3 mb-1.5">
                        {{-- <form @submit.prevent.stop="searchlead();" id="lead-search-form" class=" relative mx-auto text-base-content  rounded-lg"> --}}
                            <input class="  bg-base-100 input input-sm  focus:outline-none focus:ring-0 focus-within:border-0 text-base-content"
                              type="text" name="search" id="search-input" placeholder="Search name or phone" :value="search != '' ? search : search">
                            <button type="submit" @click.prevent.stop="searchlead();" class=" ">
                              <x-icons.search-icon/>
                            </button>
                        {{-- </form> --}}
                    </div>

                    <button @click.prevent.stop="$dispatch('linkaction',{link:'{{route('followups')}}', route: 'followups', fragment: 'page-content', fresh: true})" class=" btn btn-sm btn-ghost h-fit self-end ml-3 mb-1.5">
                        <x-icons.refresh-icon/>
                    </button>
                </div>

            </form>
        </div>

      </div>

      <x-modals.display-image/>

      <div class="lg:h-[calc(100vh-5.875rem)] pt-7 pb-[2.8rem] bg-base-200 w-full flex flex-col lg:flex-row justify-start items-center lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">



        {{-- followups table --}}
        <x-tables.followup-table :followups="$followups"/>

        {{-- details section --}}
        <div
        x-data = "{
                show_remarks_form: false,
                fpLoading: false
            }"
        class=" w-[96%] lg:w-[50%] min-h-[100%] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <h1 class="text-lg text-secondary font-semibold text-center">Follow up details</h1>
            <p x-show="!fpselected" class=" font-semibold text-base text-center mt-4">Select a follow up...</p>

            <div x-show="fpLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                <span class="loading loading-bars loading-md "></span>
            </div>

            <x-helpers.lead-segment-helper/>
            <div x-show="fpselected && !fpLoading" class="flex w-full mt-3">
                <div
                {{-- updating values in the details section --}}
                @fpupdate.window="
                fpLoading = true;
                showconsultform = false;
                $dispatch('resetsection');
                appointment = $event.detail.appointment;
                if(fps[$event.detail.id] != null || fps[$event.detail.id] != undefined){
                    fp = fps[$event.detail.id];
                    fpname = fp.lead.name;
                    lead = fp.lead;
                    qnas = lead.qnas;
                }
                else{
                    fp = $event.detail.followup;
                    fp.lead = $event.detail.lead;
                    lead = fp.lead;
                    lead.qnas = $event.detail.qnas;
                    qnas = lead.qnas;
                    lead.appointment = $event.detail.appointment;
                    leadremarks = $event.detail.lead_remarks;
                    fp.lead.remarks = leadremarks;
                    fps[fp.id] = fp;
                }
                fpselected = true;
                isValid = fp.lead.is_valid;
                isGenuine = fp.lead.is_genuine;
                fpname = fp.lead.name;
                axios.get('/api/followup',{
                    params: {
                    id: fp.id,
                    lead_id: fp.lead.id

                    }
                  }).then(function (response) {
                    fphistory = response.data.followup;
                    console.log(response.data.followup);
                    historyLoading = false;

                  }).catch(function (error){
                    console.log(error);
                    historyLoading = false;
                  });
                  show_remarks_form = !fp.remarks || fp.remarks.length ==0;
                  $dispatch('resetaction');
                  setTimeout(()=>{
                    fpLoading = false;
                },500);
                "
                class=" w-[44%] border-r border-primary">
                <h1 class=" font-medium text-base text-secondary">Lead details</h1>
                    <p class="font-medium">Name : <span x-text=" fp.lead != undefined ? fp.lead.name : '' "> </span></p>
                    <p class="font-medium">City : <span x-text="fp.lead != undefined ? fp.lead.city : '' "> </span></p>
                    <p class="font-medium">Phone : <span x-text=" fp.lead != undefined ? fp.lead.phone : '' "> </span></p>
                    <p class="font-medium flex space-x-1"><span>Email : <span> <span x-text=" fp.lead != undefined ? fp.lead.email : '' "> </span>
                        <a class=" btn btn-xs btn-ghost"
                        @click.prevent.stop="$dispatch('linkaction',{
                            link: '{{route('email.compose',['id'=>'_X_'])}}'.replace('_X_',lead.id),
                            route: 'email.compose',
                            fragment: 'page-content'
                        })"><x-icons.envolope-icon/></a>
                    </p>

                    <div class=" flex items-center space-x-2">
                        <p class=" font-medium">Is valid : </p>

                        <input  type="checkbox" name="is_valid"  :checked=" isValid == 1 ? true : false" class="checkbox checkbox-sm cursor-not-allowed pointer-events-none checkbox-success focus:ring-0" />
                    </div>

                    <div class=" flex items-center space-x-2  ">
                        <p class=" font-medium ">Is genuine : </p>

                        <input  type="checkbox" name="is_genuine"  :checked=" isGenuine == 1 ? true : false " class="checkbox checkbox-sm cursor-not-allowed pointer-events-none checkbox-success focus:ring-0" />
                    </div>

                    <p class="font-medium">Source : <span x-text=" fp.lead != undefined && fp.lead.source ? fp.lead.source.name : 'UNKNOWN' "> </span></p>

                    <p class="font-medium">Campaign : <span x-text=" fp.lead != undefined && fp.lead.campaign != '' ? fp.lead.campaign : 'UNKNOWN' "> </span></p>

                    <p class="font-medium">Lead Segment : <span class=" uppercase !text-warning" x-text="fp.lead != undefined && fp.lead.customer_segment != null ? fp.lead.customer_segment : 'Unknown' "></span></p>

                    {{-- Lead segment deciding questions --}}
                    <x-dropdowns.lead-segment-questions/>
                    {{-- the events dispatched from this component is handled by a handlers written inside lead-segment-helper component --}}

                    <p class="font-medium">Lead Status: <span class=" uppercase !text-warning" x-text="fp.lead != undefined && fp.lead.status != null ? fp.lead.status : '-' "></span></p>

                    <p x-show=" fp.lead != undefined && fp.lead.status == 'Consulted' " class="font-medium">Treatment status: <span class=" uppercase !text-warning" x-text="fp.lead != undefined && fp.lead.treatment_status != null ? fp.lead.treatment_status : '---' "></span></p>

                    <div x-show="leadremarks.length != 0" class="mt-2.5">
                        <p class=" text-base font-medium text-secondary">Lead remarks</p>

                        <ul class=" list-disc text-sm list-outside flex flex-col space-y-2 font-normal">
                            <template x-for="remark in leadremarks">

                                <li class=" space-x-2">
                                    <span x-text="remark.remark"></span>
                                    <span>-</span>
                                    <span x-text="formatDate(remark.created_at)"></span>

                                </li>

                            </template>
                        </ul>
                    </div>

                    <x-sections.followup-history/>

                </div>

                <div x-data="{
                    selected_section: 'new_follow_up',
                    messageLoading : false,
                    chats : [],
                    custom_enabled: false,
                    loadWhatsApp(){
                        $dispatch('resetselect');
                        this.selected_section = 'wp';
                        this.messageLoading = true;

                        axios.get('/api/get/chats',{
                            params : {
                                id : lead.id
                            }
                        }).then((r)=>{
                            this.expiry_timestamp = r.data.expiration_time;
                            this.checkExpiry(this.expiry_timestamp);
                            console.log(r);
                            this.chats = r.data.chats;
                            this.messageLoading = false;

                        }).catch((e)=>{
                            console.log(e);
                        });

                    },
                    markasread(){
                        axios.get('/mark/read',{
                            params:{
                                lead_id: lead.id
                            }
                        }).then((r)=>{
                            console.log('marked messages as read');
                        }).catch((e)=>{
                            console.log('could not mark messages as read');
                        });
                    },
                    checkExpiry(timestamp){
                        if(timestamp == null){
                            this.custom_enabled = false;
                        }
                        else{
                            const date = new Date(timestamp * 1000);
                            const options = {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            timeZone: 'Asia/Kolkata',
                            };

                            const formattedDate = new Intl.DateTimeFormat('en-IN', options).format(date);
                            console.log(formattedDate);
                            const currentDate = new Date();
                            const timeDifference = currentDate - date;
                            const twentyFourHoursInMillis = 24 * 60 * 60 * 1000;

                            if (timeDifference >= twentyFourHoursInMillis) {
                                this.custom_enabled = false;
                            } else {
                                this.custom_enabled = true;
                            }
                        }
                    }
                }"
                @resetsection.window=" selected_section = 'new_follow_up'; "
                class=" w-[56%] px-2.5">

                <div class=" flex space-x-4">
                    <h2 @click="selected_section = 'new_follow_up'" class=" text-secondary font-medium text-base cursor-pointer" :class=" selected_section == 'new_follow_up' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">Follow up Actions</h2>

                    <h2 @click="selected_section = 'qna' " class=" text-secondary font-medium text-base cursor-pointer" :class=" selected_section == 'qna' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">QNA</h2>

                    <h2 @click="loadWhatsApp();" class=" text-secondary font-medium text-base cursor-pointer" :class=" selected_section == 'wp' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">WhatsApp</h2>
                </div>

                    <div x-show="selected_section == 'new_follow_up'" class="pt-4">

                        <div x-show="fp.lead != null && fp.lead.status == 'Appointment Fixed'">

                            <template x-if="fp.lead != null && fp.lead.status == 'Appointment Fixed'">
                                <div>
                                    <h2 class="font-medium ">Follow-up Scheduled Date: <span x-text="formatDateOnly(fp.scheduled_date)" class="text-warning"></span></h2>

                                    <h2 class="font-medium ">Appointment Scheduled Date:
                                        <span x-text="formatDateOnly(fp.lead.appointment.appointment_date)" class="text-warning"></span>
                                    </h2>
                                </div>
                            </template>
                        </div>

                        <h3 class="text-sm font-medium text-secondary">Remarks:</h3>
                            <form
                            x-data ="
                            { doSubmit() {
                                let form = document.getElementById('followup-form');
                                let formdata = new FormData(form);
                                formdata.append('followup_id',fp.id);
                                formdata.append('lead_id',fp.lead.id);
                                $dispatch('formsubmit',{url:'{{route('process-followup')}}', route: 'process-followup',fragment: 'page-content', formData: formdata, target: 'followup-form'});
                            }}"

                            @submit.prevent.stop="doSubmit();"

                            @formresponse.window="
                            console.log($event.detail.content);
                            if ($event.detail.target == $el.id) {
                                if ($event.detail.content.success) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                    $el.reset();


                                    if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                    {

                                    fp.lead.status = $event.detail.content.lead.status;
                                    fp.actual_date = $event.detail.content.followup.actual_date;

                                    }

                                    if($event.detail.content.followup_remark != null || $event.detail.content.followup_remark != undefined)
                                    {
                                        fp.remarks.push($event.detail.content.followup_remark);
                                        show_remarks_form = false;

                                    }

                                    historyLoading = true;
                                    axios.get('/api/followup',{
                                        params: {
                                        id: fp.id,
                                        lead_id: fp.lead.id

                                        }
                                    }).then(function (response) {
                                        history = response.data.followup;
                                        console.log(response.data.followup);
                                        historyLoading = false;

                                    }).catch(function (error){
                                        console.log(error);
                                        historyLoading = false;
                                    });


                                    $dispatch('formerrors', {errors: []});
                                } else if (typeof $event.detail.content.errors != undefined) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                                } else{
                                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                                }
                            }"

                            id="followup-form" class=" mt-2 bg-base-100 rounded-xl flex flex-col space-y-1" action="">

                            <ul class=" list-disc list-outside ml-2.5">
                                <template x-if="fp.remarks != undefined || fp.remarks != null">
                                    <template x-for="remark in fp.remarks">
                                        <li>
                                            <p class="flex space-x-1">
                                                <span x-text="remark.remark"></span>
                                                <span class=" font-thin text-sm" x-text="'-'+formatDate(remark.created_at)"></span>
                                            </p>
                                        </li>
                                    </template>
                                </template>
                            </ul>
                            <div x-show="!fp.remarks || fp.remarks.length == 0 || show_remarks_form">
                                <textarea name="remark" required class="textarea bg-base-200 focus:ring-0 w-full" placeholder="Add new follow up remark"></textarea>

                                <div>
                                    <button type="submit" class="btn btn-primary btn-xs mt-2 self-start">Save remark</button>
                                    <button type="submit" class=""></button>
                                </div>
                            </div>
                            <div x-show="!show_remarks_form" >
                                <button @click.prevent.submit="show_remarks_form = true;" type="submit" class="btn btn-ghost text-warning btn-xs self-end normal-case">More Remarks&nbsp;+</button>
                            </div>

                            </form>


                            {{-- mark as consulted if appointment is scheduled --}}




                            <div x-data="{
                                    selected_action : '-- Select Action --',
                                    dropdown : document.getElementById('followup-action-dropdown')
                                }"
                                @resetaction.window="selected_action = '-- Select Action --';"
                                class="pt-6 px-1"
                                x-show="fp.remarks && fp.remarks.length > 0"
                                >
                                <h3 class="text-sm font-medium text-secondary">Actions:</h3>
                                <div x-show="fp.next_followup_date && lead.status != 'Appointment Fixed'">
                                    <p class=" font-semibold text-sm text-warning">Next follow up is scheduled.</p>
                                </div>
                                <div x-show="lead.rescheduled">
                                    <p class=" font-semibold text-sm text-warning">Appointment is rescheduled.</p>
                                </div>
                                <div x-show="lead.status == 'Consulted' " >
                                    <p class="font-semibold text-sm text-warning">Consultation completed.</p>
                                </div>
                                <div x-show="lead.status == 'Completed'" >
                                    <p class="font-semibold text-sm text-warning">Process completed!</p>
                                </div>

                                <x-forms.select-treatment-status-form/>

                                <x-dropdowns.followups-action-dropdown/>

                                <x-forms.followup-add-appointment-form :doctors="$doctors"/>

                                <x-forms.lead-close-form/>
                                <x-forms.lead-complete-form/>
                                <x-forms.lead-consult-form :doctors="$doctors"/>

                                <x-forms.add-followup-form/>

                                <x-forms.reschedule-appointment :doctors="$doctors"/>

                            </div>
                    </div>


                    {{-- QNA section --}}

                    <div x-show="selected_section == 'qna'" class="p-3">
                        <x-sections.qna />
                    </div>

                    {{-- Whatsapp section --}}
                <div x-show="selected_section == 'wp' " class=" py-3" :class="messageLoading ? ' flex w-full ' : '' ">
                    <x-sections.whatsapp :templates="$messageTemplates"/>

                    <div x-show="messageLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                        <span class="loading loading-bars loading-md "></span>
                        <label for="">Please wait while we load messages...</label>
                    </div>

                </div>

            </div>

            </div>
        </div>

      </div>

    </div>
</div>
<x-footer/>
</x-easyadmin::app-layout>
