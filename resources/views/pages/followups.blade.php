<x-easyadmin::app-layout>

<div x-data="{
            fpselected : false,
            fp : [],
            lead : [],
            fps : [],
            fpname : '',
            isValid : false,
            isGenuine : false,
            fpremarks : [],
            leadremarks: [],
            historyLoading: true,
            fphistory: [],
            convert: false,
            consult: false,
            appointment: null,
            showconsultform: false,
            page: null
        }"
        x-init="
        selectedCenter = null;
        @isset($selectedCenter)
            selectedCenter = {{$selectedCenter}};
        @endisset
        page = getPage();"

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('linkaction',{
            link: $event.detail.link,
            route: currentroute,
            fragment: 'page-content',
        })"

        >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div class=" flex justify-start items-center w-full bg-base-200 pt-1.5 pl-[3.3%] space-x-2">
        <h1 class=" text-primary text-xl font-semibold bg-base-200 ">Pending follow ups</h1>

        <div>
            @can('is-admin')
                @php
                $route = "followups";
                @endphp
                <x-forms.filter-leads :route="$route" :centers="$centers"/>
            @endcan
        </div>

      </div>


      <div class="lg:h-[calc(100vh-5.875rem)] pt-7 pb-[2.8rem] bg-base-200 w-full flex flex-col lg:flex-row justify-evenly items-center lg:items-start space-y-4 lg:space-y-0">

        {{-- followups table --}}
        <x-tables.followup-table :followups="$followups"/>

        {{-- details section --}}
        <div
        x-data = "{
                show_remarks_form: false
            }"
        class=" w-[96%] lg:w-[50%] min-h-[100%] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <h1 class="text-lg text-secondary font-semibold text-center">Follow up details</h1>
            <p x-show="!fpselected" class=" font-semibold text-base text-center mt-4">Select a follow up...</p>

            <div x-show="fpselected" class="flex w-full mt-3">
                <div
                {{-- updating values in the details section --}}
                @fpupdate.window="
                showconsultform = false;
                $dispatch('resetsection');
                appointment = $event.detail.appointment;
                if(fps[$event.detail.id] != null || fps[$event.detail.id] != undefined){
                    fp = fps[$event.detail.id];
                    fpname = fp.lead.name;
                    lead = fp.lead;
                }
                else{
                    fp = $event.detail.followup;
                    fp.lead = $event.detail.lead;
                    lead = fp.lead;
                    lead.appointment = $event.detail.appointment;
                    leadremarks = $event.detail.lead_remarks;
                    fp.lead.remarks = leadremarks;
                    fps[fp.id] = fp;
                }
                console.log(fp.remarks);
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
                "
                class=" w-[40%] border-r border-primary">
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

                    <p class="font-medium">Lead Segment : <span class=" uppercase !text-warning" x-text="fp.lead != undefined && fp.lead.customer_segment != null ? fp.lead.customer_segment : 'Unknown' "></span></p>

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
                    qnas: [],
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
                class=" w-[60%] px-2.5">

                <div class=" flex space-x-4">
                    <h2 @click="selected_section = 'new_follow_up'" class=" text-secondary font-medium text-base cursor-pointer" :class=" selected_section == 'new_follow_up' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">Follow up Actions</h2>
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
                                <x-forms.lead-consult-form/>

                                <x-forms.add-followup-form/>

                                <x-forms.reschedule-appointment :doctors="$doctors"/>

                            </div>
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
