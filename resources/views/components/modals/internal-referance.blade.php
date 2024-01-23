@props(['centers'])
<div x-show="referLead" x-cloak x-transition class=" absolute w-full h-screen z-30 bg-neutral bg-opacity-70">

    <div class="md:w-[40%] h-fit rounded-lg bg-base-100 mx-auto mt-14 bg-opacity-100 flex flex-col items-center p-4">
        <h1 class="text-secondary font-medium text-lg uppercase">Internal Reference</h1>

        <form x-data="{
            agents: [],
            showCampaignForm: false,
            {{-- sources: [],
            campaigns: [], --}}
            campaignName: '',
            loadAgents(cid){
                axios.get('{{route('center.agents')}}',{
                    params:{
                        cid : cid
                    }
                }).then((r)=>{
                    this.agents = r.data.agents;
                }).catch((e)=>{
                    console.log(e);
                });
            },
            loadCampaigns() {
                $dispatch('formsubmit', {url: '{{route('campaign.all')}}', target: 'lead-campaigns'});
            },
            createCampaign(campaign) {
                let fd = new FormData();
                fd.append('name', campaign);
                $dispatch('formsubmit', { url: '{{ route('campaign.store') }}', route: 'campaign.store', fragment: 'page-content', formData: fd, target: 'new-campaign-iref-form' });
                showCampaignForm = false;
            }
        }"
        @submit.prevent.stop="referNewLead($el, '{{route('lead.refer')}}')"
        id="refer-lead-form" action="" class=" flex flex-col space-y-2 mt-4 w-full items-center text-base-content"
        @formresponse.window="
        if($event.detail.target == 'new-campaign-iref-form'){
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
            referLead = false;
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
                    <select required name="center" id="center-refer" class=" select min-w-72 md:w-96 select-bordered border-secondary" @change="if($el.value){loadAgents($el.value)}">
                        <option value="">Select center</option>
                        @foreach ($centers as $center)
                            <option value="{{$center->id}}">{{$center->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class=" flex-col flex">
                    <label for="" class="font-medium text-base-content">Agent :</label>
                    <select required name="agent" id="agent-refer" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                        <template x-for="agent in agents">
                            <option :value="agent.id" x-text="agent.name"></option>
                        </template>
                    </select>
                </div>

                <div class=" flex-col flex">
                    <label for="" class="font-medium text-base-content">Source :</label>
                    <select required name="source" id="lead-refer-source" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                        <template x-for="source in sources">
                            <template x-if="source.forms.includes('Internal Reference')">
                                <option :value="source.id" x-text="source.name"></option>
                            </template>
                        </template>
                    </select>
                </div>

                <div class=" flex-col flex">
                    <label for="" class="font-medium text-base-content">Campaign :</label>
                    <select required name="campaign" id="lead-source" class=" select min-w-72 md:w-96 select-bordered border-secondary">
                        <option value="">Select One</option>
                        <template x-for="c in campaigns">
                            <option :value="c.name"><span x-text="c.name"></span></option>
                        </template>
                    </select>
                </div>


            <div class=" flex justify-between md:w-96">
                <button @click.prevent.stop="referLead = false;" class=" btn btn-error btn-sm">Cancel</button>
                <button type="submit" class=" btn btn-success btn-sm">Save</button>
            </div>
            {{-- <x-modals.new-campaign-modal formId="create-campaign-iref"/> --}}
        </form>
    </div>

</div>
