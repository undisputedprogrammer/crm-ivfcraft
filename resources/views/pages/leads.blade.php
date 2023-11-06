<x-easyadmin::app-layout>
<div x-data="x_leads" x-init="
        selectedCenter = null;
        @isset($selectedCenter)
            selectedCenter = '{{$selectedCenter}}';
        @endisset
        @isset($status)
            selectedStatus = '{{$status}}';
        @endisset
        theLink = '{{route('fresh-leads')}}';
        @isset($is_valid)
            is_valid = '{{$is_valid}}';
        @endisset
        @isset($is_genuine)
            is_genuine = '{{$is_genuine}}';
        @endisset
        @isset($creation_date)
            creation_date = '{{$creation_date}}';
        @endisset
        @isset($processed)
            isProcessed = true;
        @endisset"
    >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}

        <div class=" flex bg-base-200 items-center justify-between px-[1.25%]">

            <div class=" flex space-x-3 items-center justify-start pt-1.5">
                <h1 class=" text-primary text-xl font-semibold bg-base-200 ">Leads</h1>
                <div class=" flex flex-col md:flex-row space-y-2 items-center md:space-y-0 space-x-3">
                    @can('is-admin')
                        @php
                        $route = "fresh-leads";
                        @endphp
                        <x-forms.filter-leads :route="$route" :centers="$centers"/>
                    @endcan

                    {{-- Search lead by name or phone number --}}
                    <div>
                        <form @submit.prevent.stop="searchlead();" id="lead-search-form" class=" relative mx-auto text-base-content p-1 bg-neutral rounded-lg">
                            <input class="border border-primary bg-base-100 input input-sm  focus:outline-none focus:ring-0 focus-within:border-primary text-base-content"
                              type="text" name="search" placeholder="Search name or phone">
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 ">
                              <x-icons.search-icon/>
                            </button>
                        </form>
                    </div>

                    {{-- filter lead by status --}}
                    <div>
                        <x-forms.filter-lead-by-status :status="$status"/>
                    </div>

                    <div>
                        <x-forms.filter-lead-by-date/>
                    </div>

                </div>

            </div>

            <button @click.prevent.stop="toggleTemplateModal()" x-show="Object.keys(selectedLeads).length != 0" x-transition x-cloak class="btn btn-success flex btn-sm self-end"><x-icons.whatsapp-icon/><span>Bulk message</span>
            </button>
        </div>

        <x-modals.template-select-modal :templates="$messageTemplates"/>
        <x-display.sending/>
        <x-modals.lead-edit-modal/>
        <x-modals.create-lead-modal :centers="$centers"/>

        <div class="w-full flex bg-base-200 px-[1.25%] pt-1.5 space-x-2">

            <a x-show="!isProcessed" x-cloak class=" btn btn-outline normal-case text-primary btn-sm hover:bg-primary hover:text-black"
            @click.prevent.stop="leadsProcessedToday();">Processed Today</a>

            <a x-show="isProcessed" x-cloak href="" class=" btn btn-outline normal-case text-primary btn-sm hover:bg-primary hover:text-black"
            @click.prevent.stop="$dispatch('linkaction',{
                link: '{{route('fresh-leads')}}',
                route: 'fresh-leads',
                fragment: 'page-content',
                fresh: true
            })">Fresh leads</a>

            <a @click.prevent.stop="createLead = true;" href="" class=" btn btn-sm btn-outline btn-success">
                New lead
            </a>

        </div>


      <div x-data="{
        convert: false
      }"


        {{-- pagination event handler --}}
        @pageaction.window="
        params = {};
        selectedCenter !== null && (params.center = selectedCenter);
        if(selectedStatus != null && selectedStatus != 'none'){
            params.status = selectedStatus;
        }
        is_valid !== null && (params.is_valid = is_valid);
        is_genuine !== null && (params.is_genuine = is_genuine);

        if(Object.keys(params).length > 0){
            details = {
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
                params: params
            };
        }else{
            details = {
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content'
            };
        }

        $dispatch('linkaction', details);
        "

        {{-- Event handler to handle the change cutomer segment event --}}
        @changesegment.window="
        if($event.detail.current == $event.detail.new){
            console.log('cannot change status');
        }
        else{

        ajaxLoading = true;
        axios.get($event.detail.link,{
            params: {
                lead_id : lead.id,
                customer_segment : $event.detail.new
            }
        }).then(function(response){
            console.log(response);
            lead.customer_segment = $event.detail.new;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            console.log(error);
            ajaxLoading = false;
        });
        }"

        {{-- Change Questions --}}
        @changequestion.window="
        if($event.detail.current == $event.detail.q_answer){
            console.log('cannot change answer');
        }
        else{

        ajaxLoading = true;
        axios.get($event.detail.link,{
            params: {
                lead_id : lead.id,
                q_answer : $event.detail.q_answer,
                question : $event.detail.question
            }
        }).then(function(response){
            console.log(response);
            if(response.data.q_visit != undefined){
                if(response.data.q_visit == null || response.data.q_visit == 'null'){
                    lead.q_visit = null;
                }
                else{
                    lead.q_visit = response.data.q_visit;
                }
            }
            if(response.data.q_decide != undefined){
                if(response.data.q_decide == null || response.data.q_decide == 'null'){
                    lead.q_decide = null;
                }
                else{
                    lead.q_decide = response.data.q_decide;
                }
            }
            lead.customer_segment = response.data.customer_segment;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            console.log(error);
            ajaxLoading = false;
        });
        }"

        {{-- change is_valid status --}}
        @changevalid.window="
        ajaxLoading = true;
        axios.get($event.detail.link,{
            params:{
                lead_id : lead.id,
                is_valid : $event.detail.is_valid
            }
        }).then(function(response){

            lead.is_valid = response.data.is_valid;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            ajaxLoading = false;
            console.log(error);
        })"

        {{-- change is_genuine status --}}
        @changegenuine.window="
        ajaxLoading = true;
        axios.get($event.detail.link,{
            params:{
                lead_id : lead.id,
                is_genuine : $event.detail.is_genuine
            }
        }).then(function(response){

            lead.is_genuine = response.data.is_genuine;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            ajaxLoading = false;
            console.log(error);
        })"
       class=" md:h-[calc(100vh-5.875rem)] pt-2 pb-[2.8rem]  bg-base-200 w-full md:flex justify-evenly">

            <x-tables.leads-table :leads="$leads"/>

        <div x-data="{
                detailsLoading: false,
                show_remarks_form: false,
                selected_section: 'actions',
                messageLoading : false,
                qnas: [],
                chats : [],
                expiry_timestamp: null,
                custom_enabled: false,
                loadWhatsApp(){
                    this.selected_section = 'wp';
                    this.messageLoading = true;
                    $dispatch('resetselect');
                    axios.get('/api/get/chats',{
                        params : {
                            id : lead.id
                        }
                    }).then((r)=>{
                        this.expiry_timestamp = r.data.expiration_time;
                        this.checkExpiry(this.expiry_timestamp);
                        this.chats = r.data.chats;
                        this.messageLoading = false;
                        this.markasread();
                    }).catch((e)=>{
                        console.log(e);
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
                }
            }"

            class="w-[96%] mx-auto mt-4 md:mt-0 md:w-[50%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">

            <p x-show="!selected" x-cloak class=" font-semibold text-base text-center mt-4">Select a lead...</p>

            <div x-show="detailsLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                <span class="loading loading-bars loading-md "></span>
            </div>

            <div x-show="selected && !detailsLoading" x-transition
            @detailsupdate.window="
            detailsLoading = true;
            selected_section = 'actions';
            selected = true;
            if(leads[$event.detail.id] == undefined){
                leads[$event.detail.id] = {};
                lead = $event.detail.lead;
                followups = $event.detail.followups;
                followup_remarks = followups[0].remarks;
                qnas = $event.detail.qnas;
                name = lead.name;
                leads[lead.id] = lead;
                leads[lead.id].remarks = remarks;
                leads[lead.id].followups = followups;
                leads[lead.id].qnas = qnas;
            }
            else{
                lead = leads[$event.detail.id];
                followup_remarks = leads[$event.detail.id].followups[0].remarks;
                followups = leads[$event.detail.id].followups;
                name = lead.name;
                qnas = lead.qnas;
            }
            show_remarks_form =  !followup_remarks || followup_remarks.length == 0,
            convert = false;
            $dispatch('resetactions');
            setTimeout(()=>{
                detailsLoading = false;
            },500);
            console.log(followups[0].remarks);
            " class=" mt-2 flex flex-col lg:flex-row lg:justify-between min-h-96">

            {{-- Details section starts --}}
            <div class=" border-r border-primary pr-1.5 w-[46%]">
                <h1 class=" text-secondary font-medium text-base flex space-x-1 items-center">
                    <span>Lead Details</span>
                    <button @click.prevent.stop="editLead = true;" class=" btn btn-ghost btn-xs btn-warning text-warning">
                        <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                    </button>
                </h1>
                <div class=" mb-4">
                    <p class="text-sm font-medium">Name : <span x-text="lead.name"> </span></p>
                    <p class="text-sm font-medium">City : <span x-text="lead.city"> </span></p>
                    <p class="text-sm font-medium">Phone : <span x-text="lead.phone"> </span></p>
                    <p class="text-sm font-medium flex space-x-1 items-center"><span>Email : <span><span x-text="lead.email"> </span>
                        <a class=" btn btn-xs btn-ghost"
                        @click.prevent.stop="$dispatch('linkaction',{
                            link: '{{route('email.compose',['id'=>'_X_'])}}'.replace('_X_',lead.id),
                            route: 'email.compose',
                            fragment: 'page-content'
                        })"><x-icons.envolope-icon/></a>
                    </p>
                </div>

                <div class=" flex items-center space-x-2">
                    <p class=" text-sm font-medium">Is valid : </p>

                    <input @change.prevent.stop="$dispatch('changevalid',{
                        link: '{{route('change-valid')}}',
                        is_valid: lead.is_valid,
                    });" type="checkbox" name="is_valid" :checked=" lead.is_valid == 1 ? true : false" class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                </div>

                <div class=" flex items-center space-x-2 ">
                    <p class=" text-sm font-medium ">Is genuine : </p>

                    <input @change.prevent.stop="$dispatch('changegenuine',{
                        link: '{{route('change-genuine')}}',
                        is_genuine: lead.is_genuine,
                    });" type="checkbox" name="is_genuine" :checked=" lead.is_genuine == 1 ? true : false " class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                </div>

                {{-- Questions for lead segment --}}

                {{-- question visit within a week --}}
                <div x-data="{
                    visit_dropdown : document.getElementById('visit-question-dropdown')
                }" class="flex items-center space-x-2">
                    <p class=" text-sm font-medium">Visit within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm"
                        @click.prevent.stop="visit_dropdown.style.visibility ='visible' "><span x-text="lead.q_visit == null || lead.q_visit == 'null' ? 'Not selected' : lead.q_visit " class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul id="visit-question-dropdown" tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'null',
                                    question : 'q_visit'
                                });
                                visit_dropdown.style.visibility ='hidden';" class=" " :class="lead.q_visit == null ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'yes',
                                    question : 'q_visit'
                                });
                                visit_dropdown.style.visibility ='hidden';" class=" " :class="lead.q_visit == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'no',
                                    question : 'q_visit'
                                });
                                visit_dropdown.style.visibility ='hidden';" class="" :class="lead.q_visit == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
                        </ul>

                      </div>
                </div>

                {{-- question decide within a week --}}
                <div x-data="{
                    decide_dropdown : document.getElementById('decide-question-dropdown')
                }" x-show="lead.q_visit == 'no'" x-cloak class="flex items-center space-x-2 mt-1">
                    <p class=" text-sm font-medium">Decide within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm"
                        @click.prevent.stop="decide_dropdown.style.visibility ='visible';" ><span x-text="lead.q_decide == null || lead.q_decide == 'null' ? 'Not selected' : lead.q_decide " class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul id="decide-question-dropdown" tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'null',
                                    question : 'q_decide'
                                });
                                decide_dropdown.style.visibility = 'hidden';" class=" " :class="lead.q_decide == null ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'yes',
                                    question : 'q_decide'
                                });
                                decide_dropdown.style.visibility = 'hidden';" class=" " :class="lead.q_decide == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'no',
                                    question : 'q_decide'
                                });
                                decide_dropdown.style.visibility = 'hidden';" class="" :class="lead.q_decide == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
                        </ul>

                      </div>
                </div>

                <div class=" flex items-center space-x-2">
                    <p class=" text-sm font-medium">Lead Segment : <span x-text = "lead.customer_segment != null ? lead.customer_segment : 'Unknown' " :class="lead.customer_segment != null ? ' uppercase' : '' "></span></p>
                </div>

                <div class=" mt-2.5">
                    <h1 class=" text-secondary text-sm font-medium">Follow up details</h1>
                    <h1 x-text="lead.followup_created == 1 ? 'Follow up Initiated' : 'Follow up is not initiated for this lead' " class="  font-medium text-primary"></h1>

                    <p x-show="lead.followup_created == 1" class=" font-medium ">
                        <span>Follow up scheduled : </span>
                        <span class="text-primary" x-text="lead.followup_created == 1 ? formatDateOnly(followups[0].scheduled_date) : '---' "></span>
                    </p>
                    <p x-show="lead.status != 'Created' " class=" font-medium">
                        <span>Followed up date : </span>
                        <span class="text-primary" x-text="lead.followup_created == 1 ? formatDateOnly(followups[0].actual_date) : '---' " class="text-secondary"></span>
                    </p>

                    <p x-show="lead.status == 'Appointment Fixed' && lead.followup_created == 0"  class=" font-medium text-success my-1">Appointment Scheduled</p>

                </div>

            </div>
            {{-- Details section ends --}}

            {{-- Actions and whatsapp sections begins --}}

            <div class="pl-3 w-[52%]">
                <div class=" flex w-full space-x-5">
                    <h2 @click="selected_section = 'actions'" class="text-lg font-semibold text-secondary cursor-pointer" :class=" selected_section == 'actions' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">Lead Actions</h2>

                    <h2 @click="selected_section = 'qna'" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'qna' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">QNA</h2>

                    <h2 @click="loadWhatsApp();" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'wp' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">WhatsApp</h2>
                </div>

                {{-- Actions area --}}
                <div x-show="selected_section == 'actions' " x-transition class="">

                    <div class=" flex flex-col mt-2.5">

                        <p class=" text-sm font-medium text-secondary">Remarks</p>

                        <ul class=" list-disc text-sm list-outside flex flex-col space-y-2 font-normal ml-1.5">
                            <template x-for="remark in followup_remarks">

                                <li class="">
                                    <span x-text="remark.remark"></span>

                                    <span>-</span>
                                    <span x-text="formatDate(remark.created_at)"></span>

                                </li>

                            </template>
                        </ul>


                        {{-- bookmark --}}
                        <form x-show="lead.status == 'Created' "
                            x-data = "
                            {
                                doSubmit() {
                                    let form = document.getElementById('add-remark-form');
                                    let formdata = new FormData(form);
                                    formdata.append('remarkable_id',followups[0].id);
                                    formdata.append('remarkable_type','followup');
                                    $dispatch('formsubmit',{url:'{{route('add-remark')}}', route: 'add-remark',fragment: 'page-content', formData: formdata, target: 'add-remark-form'});
                                    show_remarks_form = false;
                                }
                            }
                        "

                        @submit.prevent.stop="doSubmit()"
                        @formresponse.window="
                            if ($event.detail.target == $el.id) {
                                if ($event.detail.content.success) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                    followup_remarks.push($event.detail.content.remark);
                                    leads[lead.id].followups.remarks = followup_remarks;
                                    $el.reset();

                                } else if (typeof $event.detail.content.errors != undefined) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                                } else{
                                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                                }
                            }"
                        action="" id="add-remark-form" class="mt-2 rounded-xl w-full max-w-[408px]">
                            <div x-show="!followup_remarks || followup_remarks.length == 0 || show_remarks_form" class="flex flex-col space-y-2 bg-base-200 p-3 rounded-xl">
                                <textarea placeholder="Remark" name="remark" required class="textarea textarea-bordered textarea-xs text-sm w-full max-w-sm" rows="2"></textarea>

                                <button type="submit" class="btn btn-primary btn-xs self-end">Save Remark</button>
                            </div>
                            <div x-show="!show_remarks_form" >
                                <button @click.prevent.submit="show_remarks_form = true;" type="submit" class="btn btn-ghost text-warning btn-xs self-end normal-case">More Remarks&nbsp;+</button>
                            </div>
                        </form>

                    </div>

                    {{-- mark failed attempts --}}
                    {{-- <x-forms.update-callStatus-form/> --}}


                    <div x-data="{
                            selected_action : 'Initiate Followup',
                            dropdown : document.getElementById('lead-action-dropdown')
                        }"
                        @resetactions.window=" console.log('captured reset')
                        selected_action = 'Initiate Followup';
                        "
                        x-show="lead.status != 'Completed' && followup_remarks.length > 0" class="pt-2.5">
                        <h3 class="text-sm font-medium text-secondary">Actions:</h3>
                        <x-dropdowns.leads-action-dropdown/>

                        <x-forms.followup-initiate-form/>

                        <x-forms.add-appointment-form :doctors="$doctors"/>

                        <x-forms.lead-close-form/>

                    </div>

                    <div x-show="lead.status == 'Completed'" class="py-6">
                        <p class=" font-semibold text-base text-secondary-focus">This lead has completed all follow ups.</p>
                    </div>

                    <div x-show="lead.status != 'Created'" class="w-full flex justify-center mt-4">
                        <button
                        @click.prevent.stop="$dispatch('linkaction',{
                            link: '{{route('followup.show',['id'=>'_X_'])}}'.replace('_X_',lead.id),
                            route: 'followup.show',
                            fragment: 'page-content',
                            fresh: true
                        });"
                        class=" btn btn-secondary btn-sm underline btn-ghost normal-case text-secondary">More actions</button>
                    </div>

                </div>
                {{-- Actions section ends --}}

                {{-- QNA section --}}
                <div x-show="selected_section == 'qna' " class=" py-3">
                    <x-sections.qna />
                </div>
                {{-- QNA section ends --}}

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
