@props(['centers','campaigns'])
<div x-data="{
    showCampaignForm: false,
}"
x-init = "setCampaignsArray('{{$campaigns}}')"
x-show="createLead" x-cloak x-transition class=" absolute w-full h-screen z-30 bg-neutral bg-opacity-70">

    <div class="md:w-[40%] h-fit rounded-lg bg-base-100 mx-auto mt-14 bg-opacity-100 flex flex-col items-center p-4">
        <h1 class="text-secondary font-medium text-lg uppercase">Create new lead</h1>

        <form x-data="{
            agents: [],
            sources: [],
            sourceId: null,
            {{-- campaigns: [], --}}
            campaignName: '',
            populateAgents(){
                let selectElement = document.getElementById('assign_to');
                    while (selectElement.firstChild) {
                        selectElement.removeChild(selectElement.firstChild);
                    }
                this.agents.forEach((agent) => {
                    let optionElement = document.createElement('option');
                    optionElement.value = agent.id;
                    optionElement.text = agent.name;
                    if(document.getElementById('center').value == agent.centers[0].id){
                        document.getElementById('assign_to').appendChild(optionElement);
                    }
                });
            },
            loadSources(){
                    axios.get('{{route('sources.fetch')}}')
                    .then( (response) => {
                        this.sources = response.data.sources;
                        this.sourceId = this.sources[0].id;
                            {{-- this.sources.forEach((source) => {
                                if(source.forms.includes('New Lead')){
                                    let optionElement = document.createElement('option');
                                    optionElement.value = source.id;
                                    optionElement.text = source.name;
                                    document.getElementById('lead-source').appendChild(optionElement);
                                }
                            }); --}}
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            loadCampaigns() {
                $dispatch('formsubmit', {url: '{{route('campaign.all')}}', target: 'lead-campaigns'});
            },
            createCampaign(campaign) {
                let fd = new FormData();
                fd.append('name', campaign);
                $dispatch('formsubmit', { url: '{{ route('campaign.store') }}', route: 'campaign.store', fragment: 'page-content', formData: fd, target: 'new-campaign-new-lead-form' });
                showCampaignForm = false;
            }
        }"
        x-init="
        loadCampaigns();
        loadSources();
        "  @submit.prevent.stop="storeLead($el, '{{route('lead.store')}}')"
        id="create-lead-form" action="" class=" flex flex-col space-y-2 mt-4 w-full items-center text-base-content"
        @formresponse.window="
        if($event.detail.target == 'lead-campaigns') {
            campaigns = $event.detail.content.campaigns;
        }
        if($event.detail.target == 'new-campaign-new-lead-form'){
            console.log($event.detail.content);
            if ($event.detail.content.success) {
                console.log('campaigns');
                console.log(campaigns);
                campaigns.push($event.detail.content.campaign);
                console.log(campaigns);
                campaignName = $event.detail.content.campaign.name;
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
            } else if (typeof $event.detail.content.errors != undefined) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

            } else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }
        }
        if($event.detail.target == $el.id){
            editLead = false;
            if ($event.detail.content.success) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                setTimeout(()=>{
                    $dispatch('linkaction', {link: '{{route('fresh-leads')}}', route: 'fresh-leads', fragment: 'page-content', fresh: true});
                }, 300);
            } else if (typeof $event.detail.content.errors != undefined) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

            } else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }
        }" autocomplete="off">

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Name :</label>
                <input required type="text" name="name" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">City :</label>
                <input required type="text" name="city" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Phone :</label>
                <input required type="phone" min="10" max="10" name="phone" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Email :</label>
                <input  required type="email" name="email" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>


            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Center :</label>
                <select required name="center" id="center" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                    @foreach ($centers as $center)
                        <option value="{{$center->id}}">{{$center->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Source :</label>
                <select x-model="sourceId" required name="source" id="lead-source-new" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                    <template x-for="s in sources">
                        <option :value="s.id"><span x-text="s.name"></span></option>
                    </template>
                </select>
            </div>
            <div class="flex-col flex">
                <label for="" class="font-medium text-base-content">Campaign :</label>
                <select required x-model="campaignName" required name="campaign" id="lead-campaign-new" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                    <option value="">Select One</option>
                    <template x-for="c in campaigns">
                        <option :value="c.name"><span x-text="c.name"></span></option>
                    </template>
                </select>
            </div>
                {{-- <div class="flex flex-row w-96 items-end justify-between">
                    <div class="w-3/4">
                        <label for="" class="font-medium text-base-content">Campaign :</label>
                        <select x-model="campaignName" required name="campaign" id="lead-campaign-new" class=" select w-full select-bordered border-secondary">
                            <template x-for="c in campaigns">
                                <option :value="c.name"><span x-text="c.name"></span></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-1/5">
                        <button @click="showCampaignForm = true" type="button" class="btn btn-sm btn-ghost text-accent">
                            New
                        </button>
                    </div>
                <div> --}}

                {{-- </div> --}}


                {{-- </div> --}}

            <div class=" flex justify-between md:w-96 ">
                <button @click.prevent.stop="createLead = false;" class=" btn btn-error btn-sm">Cancel</button>
                <button type="submit" class=" btn btn-success btn-sm">Save</button>
            </div>
            {{-- <x-modals.new-campaign-modal formId="create-campaign-new-lead"/> --}}
        </form>
    </div>
</div>
