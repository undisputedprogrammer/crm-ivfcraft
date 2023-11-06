<div x-show= "selected_action == 'Close Lead'" class=" bg-base-200 lg:w-fit rounded-lg p-3 mt-3">

    <template x-if="lead.status == 'Closed'">
        <p class=" font-medium text-error py-4 text-base">This lead is closed</p>
    </template>
    {{-- <template x-if="lead.status == 'Consulted'">
        <p class=" font-medium text-error py-4 text-base">This lead is Consulted</p>
    </template> --}}

    <form x-show="lead.status != 'Closed'" x-data="{
        doSubmit() {
            let form = document.getElementById('lead-close-form');
            let formdata = new FormData(form);
            {{-- formdata.append('no_followup',true); --}}
            formdata.append('lead_id',lead.id);
            $dispatch('formsubmit',{url:'{{route('lead.close')}}', route: 'lead.close',fragment: 'page-content', formData: formdata, target: 'lead-close-form'});
        }
    }" action="" class=" flex flex-col space-y-2" id="lead-close-form"
    @submit.prevent.stop="doSubmit()"
    @formresponse.window="
    if($event.detail.target == $el.id){
        if ($event.detail.content.success) {

            lead.status = 'Closed';
            document.getElementById('lead-tick-'+lead.id).classList.remove('hidden');
            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
            $el.reset();
        }else if (typeof $event.detail.content.errors != undefined) {
            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

        } else{
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        }
    }
    ">
        <div class="  flex flex-col space-y-2">
            <p class=" font-medium text-error">Are you sure you want to close this lead ? </p>
            <label class="cursor-pointer label justify-start p-0 space-x-2">
                <span class="label-text">Yes</span>
                <input type="checkbox" required class="checkbox checkbox-success checkbox-xs" />
            </label>
        </div>

        <button type="submit" class="btn btn-sm btn-error w-fit">Close Lead</button>

    </form>
</div>
