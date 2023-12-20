@props(['centers','campaigns'])
<div
x-init = "setCampaignsArray('{{$campaigns}}')"
x-show="createLead" x-cloak x-transition class=" absolute w-full h-screen z-30 bg-neutral bg-opacity-70">

    <div class="md:w-[40%] h-fit rounded-lg bg-base-100 mx-auto mt-14 bg-opacity-100 flex flex-col items-center p-4">
        <h1 class="text-secondary font-medium text-lg uppercase">Create new lead</h1>

        <form x-data="{
            agents: [],
            sources: [],
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
                            this.sources.forEach((source) => {
                                if(source.forms.includes('New Lead')){
                                    let optionElement = document.createElement('option');
                                    optionElement.value = source.id;
                                    optionElement.text = source.name;
                                    document.getElementById('lead-source').appendChild(optionElement);
                                }
                            });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            }
        }"  @submit.prevent.stop="storeLead($el, '{{route('lead.store')}}')"
        id="create-lead-form" action="" class=" flex flex-col space-y-2 mt-4 w-full items-center text-base-content"
        @formresponse.window="
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
                    <select x-init="loadSources();" required name="source" id="lead-source" class=" select min-w-72 md:w-96 select-bordered border-secondary">

                    </select>
                </div>

                @php
                    $inputId = "campaign-input";
                @endphp
                <x-helpers.campaign-autocomplete :inputId="$inputId"/>

            <div class=" flex space-x-2 md:w-96">
                <button type="submit" class=" btn btn-success btn-sm">Save</button>
                <button @click.prevent.stop="createLead = false;" class=" btn btn-error btn-sm">Cancel</button>
            </div>
        </form>
    </div>

</div>
