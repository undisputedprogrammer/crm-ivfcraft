@props(['options'=> [], 'fieldname', 'selectedoption' => ''])

<div x-data="{
    visible: true,
    }"
    x-init="
    {{-- $nextTick(() => {
        textval = '{{$textval}}';
        visible = textval != '';
        $dispatch('setparam', { {{$textname}}: textval});
    }); --}}
    "
    class="form-control w-full max-w-full h-full flex flex-row justify-end items-center absolute top-0 left-0 flex-grow flex-shrink">
    <div x-show="visible" class="absolute z-10 w-full max-w-full flex flex-row">
        <select x-data="{
            'val': {{$selectedoption}}
            }"
            @change.stop.prevent="$dispatch('spotfilter', {data: { {{$fieldname}}: val}});"
            x-model="val" class="select select-bordered select-sm max-w-xs py-0 m-1"
            :class="val == -1 || 'text-accent'">
            @foreach ($options as $item)
                <option value="{{ $item['key'] }}"
                @if (isset($item['selected']) && $item['selected'])
                    {{'selected'}}
                @endif
                >
                    {{ $item['text'] }}
                </option>
            @endforeach
        </select>
        <button
        @click.prevent.stop="
        {{-- if (textval=='') {
            visible=false;
        } else {
            textval='';
            visible=false; $dispatch('spotsearch', { {{$textname}}: textval});
        } --}}
        "
        class="bg-error border-none rounded-tr-lg rounded-bl-sm inline-block h-4 w-4 p-0 absolute top-0 right-0"><x-easyadmin::display.icon icon="icons.close" height="h-3" width="w-3"/></button>
    </div>
    {{-- <button class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100" @click.prevent.stop="visible=true;">
        <x-easyadmin::display.icon icon="icons.search" height="h-6" width="w-6"/>
    </button> --}}
</div>
