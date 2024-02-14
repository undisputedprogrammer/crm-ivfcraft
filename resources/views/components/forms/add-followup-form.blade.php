<div x-show= "(['Add Followup', 'Continue Medication', 'Discontinue Medication', 'Not Decided']).includes(selected_action)" class=" bg-base-200 lg:w-fit rounded-lg p-3 mt-3">
    <h1 x-show="fp.actual_date != null" class=" font-medium text-warning">Current Follow-up is complete</h1>
    <form x-show="fp.next_followup_date == null" x-transition
        x-data ="
                            { doSubmit() {
                                let form = document.getElementById('next-followup-form');
                                let formdata = new FormData(form);
                                formdata.append('followup_id',fp.id);
                                formdata.append('lead_id',fp.lead.id);
                                if(fp.converted){
                                    formdata.append('converted',fp.converted);
                                    console.log(fp.converted);
                                }
                                switch(selected_action) {
                                    case 'Continue Medication':
                                        formdata.append('lead_status', 'Continuing Medication');
                                        break;
                                    case 'Discontinue Medication':
                                        formdata.append('lead_status', 'Discontinued Medication');
                                        break;
                                    case 'Not Decided':
                                        formdata.append('lead_status', 'Undecided On Medication');
                                        break;
                                }
                                $dispatch('formsubmit',{url:'{{ route('next-followup') }}', route: 'next-followup',fragment: 'page-content', formData: formdata, target: 'next-followup-form'});
                            }}"
        @submit.prevent.stop="doSubmit();"
        @formresponse.window="
                            if ($event.detail.target == $el.id) {
                                if ($event.detail.content.success) {
                                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                    $el.reset();

                                    if($event.detail.content.followup != null && $event.detail.content.followup != undefined)
                                    {

                                    fp.next_followup_date = $event.detail.content.followup.next_followup_date;
                                    fp.actual_date = $event.detail.content.followup.actual_date;
                                    {{-- console.log('$event.detail.content.followup.remarks');
                                    console.log($event.detail.content.followup.remarks);
                                    $event.detail.content.followup.remarks.forEach((r) => {
                                        fp.remarks.push(r);
                                    }); --}}

                                    }

                                    if($event.detail.content.lead != null && $event.detail.content.lead != undefined){
                                        lead.call_status = $event.detail.content.lead.call_status;
                                        fp.lead = lead;
                                        fps[fp.id] = fp;
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
        id="next-followup-form" action="" class=" ">

        <div class=" flex flex-col">
            <label x-show="fp.next_followup_date == null" for="next-followup-date" class="text-sm font-medium">Schedule
                date for next follow up</label>

            <input x-show="fp.next_followup_date == null" id="next-followup-date" name="next_followup_date" required
                type="date" class=" rounded-lg input-info bg-base-200 w-72 mt-0.5">
        </div>

        <div class=" flex flex-col space-y-1 my-2.5">
            <p for="call_status" class=" font-medium ">Was the call answered?</p>
                <div class=" flex space-x-1 items-center">
                    <input type="radio" name="call_status" id="responsive" value="Responsive" required :checked="(['Continue Medication', 'Discontinue Medication', 'Not Decided']).includes(selected_action)">
                    <label for="responsive" >Yes</label>
                </div>

                <div class=" flex space-x-1 items-center">
                    <input type="radio" name="call_status" id="non-responsive" value="Not responsive" :disabled="(['Continue Medication', 'Discontinue Medication', 'Not Decided']).includes(selected_action)">
                    <label for="non-resposive">No</label>
                </div>
        </div>

        <button :disabled="fp.remarks && fp.remarks.length == 0 ? true : false" class=" btn btn-xs btn-primary mt-2"
            type="submit">Schedule next follow up</button>

    </form>
</div>
