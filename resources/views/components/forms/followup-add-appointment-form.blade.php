@props(['doctors'])
{{-- schedule appointment form --}}
<div x-show="selected_action == 'Schedule Appointment'" class=" bg-base-200 lg:w-fit rounded-lg p-2.5 mt-3">
<template x-if="lead.status == 'Appointment Fixed' ">
    <p class=" text-warning font-medium py-2"><span>Appointment scheduled for this lead on </span><span x-text="formatDateOnly(lead.appointment.appointment_date);" class="text-base-content"></span></p>
</template>
<template x-if="fp.next_followup_date != null">
    <p class=" text-warning font-medium py-2">
        <span>Next follow up scheduled for </span>
        <span x-text="formatDateOnly(fp.next_followup_date);" class="text-base-content"></span>
    </p>
</template>

<template x-if="lead.status == 'Closed' ">
    <p class=" text-error text-base font-medium py-4">This lead is closed!</p>
</template>

<form x-show="fp.converted != true && fp.next_followup_date == null" x-cloak x-transition
                        x-data ="
                        { doSubmit() {
                            let form = document.getElementById('appointment-form');
                            let formdata = new FormData(form);
                            formdata.append('followup_id',fp.id);
                            formdata.append('lead_id',fp.lead.id);
                            $dispatch('formsubmit',{url:'{{route('add-appointment')}}', route: 'add-appointment',fragment: 'page-content', formData: formdata, target: 'appointment-form'});
                        }}"
                        @submit.prevent.stop="doSubmit();"

                        @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                $el.reset();

                                if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                {

                                fp.lead.status = $event.detail.content.lead.status;
                                lead.status = $event.detail.content.lead.status;
                                fp.actual_date = $event.detail.content.followup.actual_date;
                                fp.converted = $event.detail.content.followup.converted;
                                fp.next_followup_date = $event.detail.content.followup.next_followup_date;

                                }

                                if($event.detail.content.appointment != null && $event.detail.content.appointment != undefined){
                                    lead.appointment = $event.detail.content.appointment;
                                    fp.lead.appointment = $event.detail.content.appointment;
                                }

                                if($event.detail.content.followup_remark != null || $event.detail.content.followup_remark != undefined)
                                {
                                    fp.remarks.push($event.detail.content.followup_remark);

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
                            }

                            else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }
                        "
                        id="appointment-form"
                         x-show="lead.status != 'Appointment Fixed' && fp.next_followup_date == null" action="" class=" mt-1.5 flex flex-col">

                            <div class=" flex flex-col">
                                <h2 x-show="fp.next_followup_date == null && fp.converted == null" class="text-sm font-medium text-secondary mb-1">Schedule appointment</h2>

                                <label for="select-doctor" class="font-medium">Select Doctor</label>
                                <select class="select select-bordered w-full lg:w-72 bg-base-200 text-base-content" name="doctor" id="select-doctor">
                                    <option disabled>Choose Doctor</option>
                                    @foreach ($doctors as $doctor)
                                    <template x-if="lead.center_id == '{{$doctor->center_id}}' ">
                                            <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                                    </template>
                                    @endforeach

                                </select>

                                <label for="appointment-date" class="font-medium">Appointment Date</label>
                                <input id="appointment-date" name="appointment_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">

                                <label for="followup-date" class="font-medium">Follow up Date</label>
                                <input id="followup-date" name="followup_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">
                            </div>

                            <button :disabled=" fp.converted == true ? true : false" class=" btn btn-xs btn-primary mt-2 w-fit self-start" type="submit">Schedule appointment</button>

                        </form>


                        {{-- *************************************************************************
                        If appointment is already scheduled.., the below portion will be shown
                        ************************************************************* --}}

                        {{-- mark consulted form --}}


                        <div x-show="fp.consulted" class="mt-4">
                            <p class=" text-success font-medium">Consult completed on <span x-text="lead.appointment != null ? lead.appointment.appointment_date : '' "></span></p>
                            <label @click.prevent.stop="showconsultform = true" class=" text-base-content font-medium mt-1" x-text="lead.appointment != null && lead.appointment.remarks != null ? lead.appointment.remarks : 'No remark made' "></label>
                        </div>
</div>
