@props(['status'])
<form action="" class=" flex space-x-2 items-center border-secondary bg-neutral p-1 rounded-lg" @submit.prevent.stop="filterByStatus($el)" id="filter-by-status-form">
    <select name="status" id="select-status" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
        <option value="none" :selected="'{{$status}}'=='null' || '{{$status}}'=='none'">Fresh Leads</option>
        <option value="all" :selected="'{{$status}}'=='all' ">All leads</option>
        @foreach (config('appSettings')['lead_statuses'] as $st)
        <template x-if="'{{$st}}' != 'Created'">
            <option value="{{$st}}" :selected="'{{$status}}' == '{{$st}}' ">{{$st}}</option>
        </template>
        @endforeach
    </select>

    <select name="is_valid" id="is-valid" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
        <option value="">Not Selected</option>
        <option :selected="is_valid == 'true'" value="true">Valid</option>
        <option :selected="is_valid == 'false'" value="false">Not Valid</option>
    </select>

    <select name="is_genuine" id="is-genuine" class="select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
        <option value="">Not Selected</option>
        <option :selected="is_genuine == 'true'" value="true">Genuine</option>
        <option :selected="is_genuine == 'false'" value="false">Not Genuine</option>
    </select>

    <button type="submit" class=" btn btn-sm btn-primary">Filter</button>
</form>
