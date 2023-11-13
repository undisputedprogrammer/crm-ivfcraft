@if ($x_target != 'indextable')
<x-easyadmin::utils.panelbase
    :x_ajax="$x_ajax"
    title="Users"
    indexUrl="{{route('users.index')}}"
    {{-- downloadUrl="{{route('users.download')}}" --}}
    selectIdsUrl="{{route('users.selectIds')}}"
    :items_count="$items_count"
    :items_ids="$items_ids"
    :total_results="$total_results"
    :current_page="$current_page"
    :results="$results"
    :results_name="$results_name"
    :unique_str="$unique_str"
    :results_json="$results_json"
    :paginator="$paginator"
    :total_cols="$total_cols"
    :adv_fields=""
    :section_id="$section_id"
    :selectionEnabled="$selectionEnabled"
    :row_counts="$row_counts">
    <x-slot:inputFields>
        @if (isset($aggregates))
            <input type="hidden" value="{{$aggregates}}" id="aggregates">
        @endif
        <input type="hidden" value="{{$results_json}}" id="results_json">
        <input type="hidden" value="{{$items_ids}}" id="itemIds">
    </x-slot>
    <x-slot:thead>
        {{$thead}}
    </x-slot>
    <x-slot:rows>
        {{$rows}}
    </x-slot>
</x-utils.panelbase>
@else
    {{$rows}}
@endif
