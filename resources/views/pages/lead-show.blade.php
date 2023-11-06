<x-easyadmin::app-layout>

    <x-sections.side-drawer />
    <div class="py-8">
        <div x-data="{
                lead: null,
                selected_section: 'details',
                messageLoading: false,
                qnas: [],
                chats: [],
                expiry_timestamp: null,
                custom_enabled: false,
                loadWhatsApp() {
                    console.log('fetching chats from lead'+lead.id);
                    this.selected_section = 'wp';
                    this.messageLoading = true;
                    $dispatch('resetselect');
                    axios.get('/api/get/chats', {
                        params: {
                            id: this.lead.id
                        }
                    }).then((r) => {
                        this.expiry_timestamp = r.data.expiration_time;
                        this.checkExpiry(this.expiry_timestamp);
                        this.chats = r.data.chats;
                        this.messageLoading = false;
                        this.markasread();
                    }).catch((e) => {
                        console.log(e);
                    });

                },
                checkExpiry(timestamp) {
                    if (timestamp == null) {
                        this.custom_enabled = false;
                    } else {
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
                markasread() {
                    axios.get('/mark/read', {
                        params: {
                            lead_id: lead.id
                        }
                    }).then((r) => {
                        console.log('marked messages as read');
                    }).catch((e) => {
                        console.log('could not mark messages as read');
                    });
                }
            }"
            x-init="
                lead = {{Js::from($lead)}};console.log(lead);
            "
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

            class="w-[96%] mx-auto mt-4 md:mt-0 md:w-3/4 min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll
            bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div class=" flex space-x-4">
                <h2 @click="selected_section = 'details'" class="text-lg font-semibold text-secondary cursor-pointer"
                    :class=" selected_section == 'details' ? 'opacity-100' : ' hover:opacity-100 opacity-40'">Lead
                    details
                </h2>

                <h2 @click="selected_section = 'qna'" class="text-lg font-semibold text-secondary cursor-pointer "
                    :class=" selected_section == 'qna' ? 'opacity-100' : ' hover:opacity-100 opacity-40'">QNA</h2>

                <h2 @click="loadWhatsApp();" class="text-lg font-semibold text-secondary cursor-pointer "
                    :class=" selected_section == 'wp' ? 'opacity-100' : ' hover:opacity-100 opacity-40'">WhatsApp</h2>
            </div>

            {{-- <p x-show="!selected" class=" font-semibold text-base text-center mt-4">Select a lead...</p> --}}

            <div x-show="selected_section == 'details'" x-transition
                {{-- deleted window event capture --}}
                class=" mt-2 flex flex-col space-y-2">
                <div class="flex flex-row space-x-4 justify-between">
                    <p class="text-base font-medium border border-base-content border-opacity-20 p-2 rounded-md">Name : <span x-text="lead.name"> </span></p>
                    <p class="text-base font-medium border border-base-content border-opacity-20 p-2 rounded-md">City : <span x-text="lead.city"> </span></p>
                    <p class="text-base font-medium border border-base-content border-opacity-20 p-2 rounded-md">Phone : <span x-text="lead.phone"> </span></p>
                    <p class="text-base font-medium border border-base-content border-opacity-20 p-2 rounded-md flex space-x-1">Email : <span x-text="lead.email"> </span>
                        <a class=" btn btn-xs btn-ghost"
                        @click.prevent.stop="$dispatch('linkaction',{
                            link: '{{route('email.compose',['id'=>'_X_'])}}'.replace('_X_',lead.id),
                            route: 'email.compose',
                            fragment: 'page-content'
                        })"><x-icons.envolope-icon/></a>
                    </p>
                </div>
                <div class="flex flex-row space-x-8">
                    <div class=" flex items-center space-x-2">
                        <p class=" text-base font-medium">Is valid : </p>

                        <input
                            @change.prevent.stop="$dispatch('changevalid',{
                    link: '{{ route('change-valid') }}',
                    is_valid: lead.is_valid,
                });"
                            type="checkbox" name="is_valid" :checked=" lead.is_valid == 1 ? true : false"
                            class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                    </div>

                    <div class=" flex items-center space-x-2 ">
                        <p class=" text-base font-medium ">Is genuine : </p>

                        <input
                            @change.prevent.stop="$dispatch('changegenuine',{
                    link: '{{ route('change-genuine') }}',
                    is_genuine: lead.is_genuine,
                });"
                            type="checkbox" name="is_genuine" :checked=" lead.is_genuine == 1 ? true : false"
                            class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                    </div>
                </div>
                {{-- Questions for lead segment --}}

                {{-- question visit within a week --}}
                <div class="flex items-center space-x-2">
                    <p class=" text-base font-medium">Will they visit within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm"><span
                                x-text="lead.q_visit == null || lead.q_visit == 'null' ? 'Not selected' : lead.q_visit "
                                class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52"
                            :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_visit,
                            q_answer : 'null',
                            question : 'q_visit'
                        });"
                                    class=" "
                                    :class="lead.q_visit == null ? ' text-primary hover:text-primary' : ''">Not
                                    selected</a></li>
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_visit,
                            q_answer : 'yes',
                            question : 'q_visit'
                        });"
                                    class=" "
                                    :class="lead.q_visit == 'yes' ? ' text-primary hover:text-primary' : ''">Yes</a>
                            </li>
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_visit,
                            q_answer : 'no',
                            question : 'q_visit'
                        });"
                                    class=""
                                    :class="lead.q_visit == 'no' ? ' text-primary hover:text-primary' : ''">No</a></li>
                        </ul>

                    </div>
                </div>


                {{-- question decide within a week --}}
                <div x-show="lead.q_visit == 'no'" class="flex items-center space-x-2">
                    <p class=" text-base font-medium">Will they decide within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm"><span
                                x-text="lead.q_decide == null || lead.q_decide == 'null' ? 'Not selected' : lead.q_decide "
                                class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52"
                            :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_decide,
                            q_answer : 'null',
                            question : 'q_decide'
                        });"
                                    class=" "
                                    :class="lead.q_decide == null ? ' text-primary hover:text-primary' : ''">Not
                                    selected</a></li>
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_decide,
                            q_answer : 'yes',
                            question : 'q_decide'
                        });"
                                    class=" "
                                    :class="lead.q_decide == 'yes' ? ' text-primary hover:text-primary' : ''">Yes</a>
                            </li>
                            <li><a @click.prevent.stop="
                        $dispatch('changequestion',{
                            link: '{{ route('lead.answer') }}',
                            current: lead.q_decide,
                            q_answer : 'no',
                            question : 'q_decide'
                        });"
                                    class=""
                                    :class="lead.q_decide == 'no' ? ' text-primary hover:text-primary' : ''">No</a>
                            </li>
                        </ul>

                    </div>
                </div>


                <div class=" flex items-center space-x-2">
                    <p class=" text-base font-medium">Lead Segment : <span
                            x-text="lead.customer_segment != null ? lead.customer_segment : 'Unknown' "
                            :class="lead.customer_segment != null ? ' uppercase' : ''"></span></p>

                </div>


                {{-- *********************************************************************************
        Remark area
        ********************************************************************************* --}}
                <div class="flex flex-row space-x-4 w-full justify-between">
                    <div class=" flex flex-col min-w-72">

                        <p class=" text-base font-medium text-secondary">Remarks</p>

                        <ul class=" list-disc text-sm list-outside flex flex-col space-y-2 font-normal">
                            <template x-for="remark in remarks">

                                <li class="">
                                    <span x-text="remark.remark"></span>

                                    <span>-</span>
                                    <span x-text="formatDate(remark.created_at)"></span>

                                </li>

                            </template>
                        </ul>

                        <form x-data="{
                            doSubmit() {
                                let form = document.getElementById('add-remark-form');
                                let formdata = new FormData(form);
                                formdata.append('remarkable_id', lead.id);
                                formdata.append('remarkable_type', 'lead');
                                $dispatch('formsubmit', { url: '{{ route('add-remark') }}', route: 'add-remark', fragment: 'page-content', formData: formdata, target: 'add-remark-form' });
                            }
                        }" @submit.prevent.stop="doSubmit()"
                            @formresponse.window="
                            if ($event.detail.target == $el.id) {
                                if ($event.detail.content.success) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                    axios.get('/api/get/remarks',{
                                        params: {
                                        remarkable_id: lead.id,
                                        remarkable_type: 'App\Models\Lead'
                                        }
                                    }).then(function (response) {

                                        remarks = response.data.remarks;
                                        leads[lead.id].remarks = remarks;
                                        document.getElementById('add-remark-form').reset();

                                    }).catch(function (error){
                                        console.log(error);
                                    });
                                    $dispatch('formerrors', {errors: []});
                                } else if (typeof $event.detail.content.errors != undefined) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                                } else{
                                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                                }
                            }"
                            action="" id="add-remark-form"
                            class=" bg-base-200 flex flex-col space-y-2 mt-2 p-3 rounded-xl w-full max-w-[408px]">

                            <textarea placeholder="Remark" name="remark" required class="textarea textarea-bordered textarea-sm w-full max-w-sm"></textarea>

                            <button type="submit" class="btn btn-primary btn-sm self-end">Add remark</button>

                        </form>

                    </div>

                    {{-- <div class="flex flex-col"> --}}
                        <div>
                            <h1 class=" text-secondary text-base font-medium">Follow up details</h1>
                            <h1 x-text="lead.followup_created == 1 ? 'Follow up Initiated' : 'Follow up is not initiated for this lead' "
                                class="  font-medium text-primary"></h1>

                            <p x-show="lead.followup_created == 1" class=" font-medium ">
                                <span>follow up scheduled : </span>
                                <span class="text-primary"
                                    x-text="lead.followup_created == 1 ? lead.followups[0].scheduled_date : '' "></span>
                            </p>
                            <p x-show="lead.followup_created == 1" class=" font-medium">
                                <span>Followed up date : </span>
                                <span class="text-primary"
                                    x-text="lead.followup_created == 1 && lead.followups[0].actual_date != null ? lead.followups[0].actual_date : '---' "
                                    class="text-secondary"></span>
                            </p>

                            <p x-show="lead.status == 'Appointment Fixed' && lead.followup_created == 0"
                                class=" font-medium text-success my-1">Appointment Scheduled</p>

                        </div>

                        <div x-data="{
                                selected_action: 'Initiate Followup'
                            }" class="pt-2.5 min-w-72 max-w-72">

                            <x-dropdowns.leads-action-dropdown />

                            <x-forms.followup-initiate-form />

                            <x-forms.add-appointment-form :doctors="$doctors" />

                            <x-forms.lead-close-form />

                        </div>
                    {{-- </div> --}}
                </div>


            </div>

            {{-- QNA section --}}
            <div x-show="selected_section == 'qna' " class=" py-3">
                <x-sections.qna />
            </div>


            {{-- Whatsapp section --}}
            <div x-show="selected_section == 'wp' " class=" py-3" :class="messageLoading ? ' flex w-full ' : ''">
                <x-sections.whatsapp :templates="$messageTemplates" />

                <div x-show="messageLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                    <span class="loading loading-bars loading-md "></span>
                    <label for="">Please wait while we load messages...</label>
                </div>

            </div>

        </div>
    </div>
</x-easyadmin::app-layout>
