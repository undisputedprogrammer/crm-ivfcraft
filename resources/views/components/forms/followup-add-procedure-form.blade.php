@props(['doctors'])
{{-- schedule procedure form --}}
<div x-show="selected_action == 'Schedule Procedure'" class=" bg-base-200 lg:w-fit rounded-lg p-2.5 mt-3">
<template x-if="lead.status == 'procedure Fixed' ">
    <p class=" text-warning font-medium py-2"><span>procedure scheduled for this lead on </span><span x-text="formatDateOnly(lead.procedure.procedure_date);" class="text-base-content"></span></p>
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

<form x-show="!['Procedure Scheduled', 'Completed', 'Closed'].includes(lead.status)" x-cloak x-transition
                        x-data ="
                        { doSubmit() {
                            let form = document.getElementById('procedure-form');
                            let formdata = new FormData(form);
                            formdata.append('followup_id',fp.id);
                            formdata.append('lead_id',fp.lead.id);
                            $dispatch('formsubmit',{url:'{{route('add-procedure')}}', route: 'add-procedure',fragment: 'page-content', formData: formdata, target: 'procedure-form'});
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
                                document.getElementById('status-'+lead.id).innerText = lead.status;
                                }

                                if($event.detail.content.lead != null && $event.detail.content.lead != undefined){
                                    lead.call_status = $event.detail.content.lead.call_status;
                                    fp.lead = lead;
                                    fps[fp.id] = fp;
                                }

                                if($event.detail.content.procedure != null && $event.detail.content.procedure != undefined){
                                    lead.procedure = $event.detail.content.procedure;
                                    fp.lead.procedure = $event.detail.content.procedure;
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
                        id="procedure-form"
                         x-show="lead.status != 'procedure Fixed' && fp.next_followup_date == null" action="" class=" mt-1.5 flex flex-col">

                            <div class=" flex flex-col">
                                <h2 x-show="fp.next_followup_date == null && fp.converted == null" class="text-sm font-medium text-secondary mb-1">Schedule procedure</h2>

                                <label for="procedure-select-doctor" class="font-medium">Select Doctor</label>
                                <select class="select select-bordered w-full lg:w-72 bg-base-200 text-base-content" name="doctor" id="procedure-select-doctor">
                                    <option disabled>Choose Doctor</option>
                                    @foreach ($doctors as $doctor)
                                    <template x-if="lead.center_id == '{{$doctor->center_id}}' ">
                                            <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                                    </template>
                                    @endforeach

                                </select>

                                <label for="procedure-date" class="font-medium">Procedure Date</label>
                                <input id="procedure-date" name="procedure_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">

                                <label for="followup-date" class="font-medium">Follow up Date</label>
                                <input id="procedure-followup-date" name="followup_date" required type="date" class=" rounded-lg input-info bg-base-200 w-full lg:w-72 mt-1.5">
                            </div>

                            <button class=" btn btn-xs btn-primary mt-2 w-fit self-start" type="submit">Schedule procedure</button>

                        </form>


                        {{-- *************************************************************************
                        If procedure is already scheduled.., the below portion will be shown
                        ************************************************************* --}}

                        {{-- mark consulted form --}}


                        {{-- <div x-show="fp.consulted" class="mt-4">
                            <p class=" text-success font-medium">Consult completed on <span x-text="lead.procedure != null ? lead.procedure.procedure_date : '' "></span></p>
                            <label @click.prevent.stop="showconsultform = true" class=" text-base-content font-medium mt-1" x-text="lead.procedure != null && lead.procedure.remarks != null ? lead.procedure.remarks : 'No remark made' "></label>
                        </div> --}}
</div>
