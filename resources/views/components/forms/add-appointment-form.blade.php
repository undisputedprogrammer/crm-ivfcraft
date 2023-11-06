@props(['doctors'])
{{-- schedule appointment form --}}
<div x-show="selected_action == 'Schedule Appointment'">


<template x-if="lead.status == 'Appointment Fixed' ">
    <p class=" text-primary font-medium py-4">Appointment is scheduled scheduled for this lead.</p>
</template>

<template x-if="lead.status == 'Closed' ">
    <p class=" text-error text-base font-medium py-4">This lead is closed!</p>
</template>

<form x-show=" lead.status != 'Appointment Fixed' && lead.status != 'Closed'" x-cloak x-transition
x-data ="
{ doSubmit() {
    let form = document.getElementById('appointment-form');
    let formdata = new FormData(form);
    formdata.append('followup_id',followups[0].id);
    formdata.append('lead_id',lead.id);
    $dispatch('formsubmit',{url:'{{route('add-appointment')}}', route: 'add-appointment',fragment: 'page-content', formData: formdata, target: 'appointment-form'});
}}"
@submit.prevent.stop="doSubmit();"

@formresponse.window="
if ($event.detail.target == $el.id) {
    if ($event.detail.content.success) {
        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
        $el.reset();

        if($event.detail.content.lead != null && $event.detail.content.lead != undefined)
        {

        lead.status = $event.detail.content.lead.status;
        leads[lead.id].status = lead.status;
        document.getElementById('lead-tick-'+lead.id).classList.remove('hidden');
        lead.followup_created = $event.detail.content.lead.followup_created;
        leads[lead.id].followup_created = lead.followup_created;
        }

        if($event.detail.content.followup != null && $event.detail.content.followup != undefined){

            followups.push($event.detail.content.followup);
            leads[lead.id].followups = followups;
        }

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
    }

    else if (typeof $event.detail.content.errors != undefined) {
        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

    } else{
        $dispatch('formerrors', {errors: $event.detail.content.errors});
    }
}
"
id="appointment-form"
 x-show="lead.status != 'Appointment Fixed' && lead.followup_created == false" action="" class=" mt-1.5">

    <div class="bg-base-200 p-3 rounded-lg">
        {{-- <h2 class="text-sm font-medium text-secondary mb-1">Schedule appointment</h2> --}}

        <label for="select-doctor" class="font-medium">Select Doctor</label>
        <select id="select-doctor" class="select text-sm select-bordered w-full max-w-sm bg-base-200 text-base-content" name="doctor">
            <option disabled>Choose Doctor</option>

            @foreach ($doctors as $doctor)
                <template x-if="lead.center_id == '{{$doctor->center_id}}' ">
                    <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                </template>
            @endforeach

        </select>

        <label for="appointment-date" class="font-medium">Choose Appointment date</label>
        <input  id="appointment-date" name="appointment_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full mt-1.5 max-w-sm">

        <label for="followup-date" class="font-medium">Choose Follow up date</label>
        <input  id="followup-date" name="followup_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full mt-1.5 max-w-sm">
    </div>

    <button :disabled=" lead.status == 'Appointment Fixed' ? true : false" class=" btn btn-xs btn-primary mt-2" type="submit">Schedule appointment</button>

</form>
</div>
