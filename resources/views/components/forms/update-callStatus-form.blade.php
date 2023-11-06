<div x-data="{
    formSelector: document.getElementById('form-selector'),
    showForm: false,
    toggle(){
        if(this.formSelector.checked){
            this.showForm = true;
        }
        else{
            this.showForm = false;
        }
    },
    doSubmit(){
        let form = document.getElementById('callStatus-update-form');
        let formdata = new FormData(form);
        formdata.append('lead_id',lead.id);
        $dispatch('formsubmit',{url: '{{route('callStatus.update')}}', route: 'callStatus.update', formData: formdata, target: 'callStatus-update-form'})
    }
}" class=" flex flex-col my-2.5">
    <div class=" flex space-x-2 items-center">
        <p class=" font-medium">Couldn't connect ?</p>
        <input @change="toggle();" type="checkbox" id="form-selector" class=" checkbox checkbox-secondary checkbox-xs">
    </div>

    <form id="callStatus-update-form" action="" x-show="showForm" x-transition class=" mt-1.5 flex flex-col p-3 rounded-lg bg-base-200"
    @submit.prevent.stop="doSubmit();"
    @formresponse.window="
    if($el.id == $event.detail.target){
        if($event.detail.content.success){
            lead.call_status = $event.detail.content.lead.call_status;
            lead.failed_attempts = $event.detail.content.lead.failed_attempts;
            if(typeof leads !== 'undefined'){
                leads[lead.id].call_status = lead.call_status;
                leads[lead.id].failed_attempts = lead.failed_attempts;
            }
            if(typeof fps !== 'undefined'){
                fps[fp.id].lead = lead;
            }
            $dispatch('showtoast',{mode:'success', message: $event.detail.content.message});
        }
        else if(typeof $event.detail.content.errors != undefined) {
            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

        } else{
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        }
    }">
        <label for="call-status" class=" font-medium text-xs">Call Status :</label>
        <select name="call_status" id="call-status" required class=" select select-sm text-xs font-medium select-bordered w-fit">
            <option disabled :selected="lead.call_status == null"> -- Not selected --</option>
            @foreach (config('appSettings.call_statuses') as $callStatus)
                <option value="{{$callStatus}}" :selected="lead.call_status == '{{$callStatus}}' ">{{$callStatus}}</option>
            @endforeach
        </select>

        <label for="attempts-counter" class=" font-medium mt-1 text-xs">Attempts</label>
        <input :value="lead.failed_attempts" required min="0" type="number" id="attempts-counter" name="failed_attempts" class=" input input-sm hide-btns w-fit input-bordered focus:outline-none">

        <button type="submit" class=" btn btn-primary btn-sm w-fit mt-1">Save</button>
    </form>
</div>
