@props(['doctors'])
{{-- schedule appointment form --}}
<template x-if="lead.appointment != null">
<div x-show="selected_action == 'Reschedule Appointment' && !lead.rescheduled" class=" bg-base-200 p-3 rounded-lg mt-3 lg:w-fit">

<template x-if="fp.next_followup_date != null">
    <p class=" text-primary font-medium py-4">
        <span>Next follow up scheduled for </span>
        <span x-text="getDateWithoutTime(fp.next_followup_date);"></span>
    </p>
</template>



<template x-if="lead.status == 'Closed' ">
    <p class=" text-error text-base font-medium py-4">This lead is closed!</p>
</template>

<form x-cloak x-transition
                        x-data ="
                        { doSubmit() {
                            let form = document.getElementById('edit-appointment-form');
                            let formdata = new FormData(form);
                            formdata.append('followup_id',fp.id);
                            formdata.append('lead_id',fp.lead.id);
                            formdata.append('appointment_id',lead.appointment.id);
                            $dispatch('formsubmit',{url:'{{route('appointment.update')}}', route: 'update-appointment',fragment: 'page-content', formData: formdata, target: 'edit-appointment-form'});
                        }}"
                        @submit.prevent.stop="doSubmit();"

                        @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            console.log('response from reschedule');
                            console.log($event.detail);
                            if ($event.detail.content.success == true) {
                                $dispatch('showtoast', {message: 'Appointment Rescheduled', mode: 'success'});
                                $el.reset();

                                if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                {
                                fp.scheduled_date = $event.detail.content.next_followup.scheduled_date;
                                fp.lead.appointment.appointment_date = $event.detail.content.appointment.appointment_date;
                                fp.lead.status = $event.detail.content.followup.lead.status;
                                fp.actual_date = $event.detail.content.followup.actual_date;
                                fp.converted = $event.detail.content.followup.converted;
                                fp.next_followup_date = $event.detail.content.followup.next_followup_date;
                                fp.rescheduled = true;
                                lead.rescheduled = true;
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
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }
                        "
                        id="edit-appointment-form"
                          action="" class=" mt-1.5">

                            <div class=" flex flex-col">
                                <h2 class="text-sm font-medium text-secondary mb-1">Schedule appointment</h2>

                                <template x-if="lead.appointment != null">
                                    <h3 class=" font-semibold my-1">
                                        <span>Current Appointment is with </span>
                                        <span class="text-primary" x-text="lead.appointment.doctor ? lead.appointment.doctor.name : ''"></span>
                                    </h3>
                                </template>

                                <label for="doctor" class="font-medium">Select Doctor</label>
                                <select class="select select-bordered w-full lg:w-72 bg-base-200 text-base-content" name="doctor" id="doctor">
                                    <option disabled>Choose Doctor</option>
                                    @foreach ($doctors as $doctor)
                                    <template x-if="lead.center_id == '{{$doctor->center_id}}' ">
                                            <option :selected="lead.appointment.doctor_id == '{{$doctor->id}}'" value="{{$doctor->id}}">{{$doctor->name}}</option>
                                    </template>
                                    @endforeach

                                </select>

                                <label for="new-appointment-date" class="font-medium">Appointment Date</label>
                                <input id="new-appointment-date" name="appointment_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">

                                <label for="new-followup-date" class="font-medium">Follow up Date</label>
                                <input id="new-followup-date" name="followup_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">
                            </div>

                            <button class=" btn btn-xs btn-primary mt-2 w-fit self-start" type="submit">Reschedule appointment</button>

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
</template>
