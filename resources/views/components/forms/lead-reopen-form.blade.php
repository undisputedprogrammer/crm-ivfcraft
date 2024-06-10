<div x-show="fp.lead.status == 'Closed'" x-data="{
        showReopen: false,
        reopen(el) {
            let fd = new FormData(el);
            $dispatch('formsubmit', {url: '{{route('lead.status.update')}}', formData: fd, target: 'reopen'});
        }
    }"
    @formresponse.window="
        if($event.detail.target == 'reopen') {
            $dispatch('showtoast', {messae: 'Lead reopened!', mode: 'success'});
            $dispatch('linkaction', {
                route: 'followup.show',
                link:'{{route('followup.show', ['id'=>'_X_'])}}'.replace('_X_', fp.lead.id),
                fragment: 'page-content',
                fresh: true}
            );
        }
    "
    class="mt-4"
    >
    <div x-show="!showReopen" class="text-right">
        <button @click="showReopen = true;" class="btn btn-sm btn-warning" >Re-opn lead</button>
    </div>
    <form class="border rounded-md border-base-content border-opacity-30 p-3" x-show="showReopen" @submit.prevent.stop="reopen($el);" action="">
        <h3 class="font-bold text-warning underline">Re-open lead</h3>
        <input type="hidden" name="lead_id" :value="fp.lead.id">
        <div>
            <label class="label">Select Status</label>
            <select name="status" id="select-status" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none select-bordered" required>
                <option value="" >--Select One--</option>
                @foreach (config('appSettings')['reopen_lead_statuses'] as $st)
                    @if (!str_starts_with($st, 'At Least'))
                    <option value="{{$st}}">{{$st}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class=" flex flex-col">
            <label  for="reopen-followup-date" class="text-sm font-medium">Schedule
                date for next follow up</label>

            <input x-show="fp.next_followup_date == null" id="reopen-followup-date" name="followup_date" required
                type="date" class=" rounded-lg input input-info w-72 mt-0.5">
        </div>
        <div>
            <label class="label">Remarks</label>
            <textarea name="remarks" class="textarea textarea-bordered w-full"></textarea>
        </div>
        <div class="my-3 text-right">
            <button type="submit" class="btn btn-sm btn-success">Re-open</button>
        </div>
    </form>
</div>
