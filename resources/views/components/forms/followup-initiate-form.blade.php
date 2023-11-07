<div x-show="selected_action == 'Initiate Followup'">

    <div x-show="lead.status != 'Created'">
        <p class=" text-primary font-medium py-4">Follow started</p>
    </div>

    <div x-show="lead.status == 'Closed'">
        <p class=" text-error font-medium py-4 text-base">This lead is closed.</p>
    </div>

    <form x-show="lead.status == 'Created' && lead.status != 'Closed'" x-data="{
        doSubmit() {
                let form = document.getElementById('initiate-followup-form');
                let formdata = new FormData(form);
                formdata.append('lead_id', lead.id);

                $dispatch('formsubmit', { url: '{{ route('initiate-followup') }}', route: 'initiate-followup', fragment: 'page-content', formData: formdata, target: 'initiate-followup-form' });
            }
        }"
        @formresponse.window="
            if ($event.detail.target == $el.id) {
                if ($event.detail.content.success) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                    $el.reset();

                    followups[0].actual_date = $event.detail.content.completed_followup.actual_date;
                    followups[0].next_followup_date = $event.detail.content.completed_followup.next_followup_date;
                    followups.push($event.detail.content.followup);
                    leads[lead.id].followups = followups;


                    lead.followup_created = 1;
                    lead.status = 'Follow-up Started';
                    leads[lead.id].followup_created = lead.followup_created;

                    document.getElementById('lead-tick-'+lead.id).classList.remove('hidden');
                    $dispatch('formerrors', {errors: []});
                } else if (typeof $event.detail.content.errors != undefined) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                } else{
                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                }
            }"
        id="initiate-followup-form"
        @submit.prevent.stop="doSubmit();"
        action=""
        class="bg-base-200 flex flex-col space-y-2 mt-2 p-3 rounded-xl w-full max-w-[408px]">

        <label for="scheduled-date" class="text-sm font-medium">Schedule a date for next follow up</label>
        <input id="scheduled-date" required name="scheduled_date" type="date" class=" rounded-lg input-info bg-base-100">

        <div class=" flex flex-col space-y-1 my-2.5">
            <p for="call_status" class=" font-medium ">How was the call ?</p>
                <div class=" flex space-x-1 items-center">
                    <input type="radio" name="call_status" id="responsive" value="Responsive" required>
                    <label for="responsive">Responsive</label>
                </div>

                <div class=" flex space-x-1 items-center">
                    <input type="radio" name="call_status" id="non-responsive" value="Not responsive" >
                    <label for="non-resposive">Not responsive</label>
                </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm mt-1 self-start">Add follow up</button>


    </form>
</div>
