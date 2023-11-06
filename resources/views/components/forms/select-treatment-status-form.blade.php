<div x-show=" lead.status == 'Consulted' && fp.next_followup_date == null " class=" my-3">
<form x-show=" fp.actual_date == null "
x-data="{
    doSubmit(){
        let formdata = new FormData($el);
        formdata.append('lead_id', lead.id);
        $dispatch('formsubmit',{url: '{{route('treatmentStatus.update')}}', route: 'treatmentStatus.update', formData: formdata, target: $el.id});
    }
}"
@formresponse.window="
if($event.detail.target == $el.id){
    if($event.detail.content.success){
        lead.treatment_status = $event.detail.content.treatment_status;
        fp.lead = lead;
        fps[fp.id] = fp;
        $dispatch('showtoast',{mode: 'success', message: 'Treatment status updated!'});
    }
    else{
        $dispatch('showtoast',{mode: 'error', message: 'Something went wrong!'});
    }
}"
 action="" id="set-treatment-status-form" class=" flex space-x-2 mb-1" >

    <div class=" flex flex-col">
        <label for="treatment-status" class=" font-medium">Select treatment status : </label>
        <select @change="doSubmit();" name="treatment_status" id="treatment-status" class=" select text-xs select-sm select-bordered w-64">
            <option value="" disabled :selected="lead.treatment_status == null">-- select --</option>
            <option :selected="lead.treatment_status == 'Continuing' " value="Continuing">Continue</option>
            <option :selected="lead.treatment_status == 'Discontinued' " value="Discontinued">Discontinue</option>
            <option :selected="lead.treatment_status == 'Not decided' " value="Not decided">Not decided</option>
        </select>
    </div>

    {{-- <button type="submit" class=" btn btn-primary btn-sm self-end">Save</button> --}}

</form>

<p x-show="lead.treatment_status != null" class=" flex font-medium space-x-1">
    <span>Treatment status : </span>
    <span x-text="lead.treatment_status" class=" font-semibold text-warning"></span>
</p>

</div>
