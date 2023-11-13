@props(['textval'=> '', 'textname', 'label'])

<div x-data="{
    textval: '',
    visible: false,
    }"
    x-init="
    {{-- $nextTick(() => {
        textval = '{{$textval}}';
        visible = textval != '';
        $dispatch('setparam', { {{$textname}}: textval});
    }); --}}
    textval = '{{$textval}}';
    visible = textval != '';
    "
    class="form-control w-full max-w-full h-full flex flex-row justify-end items-center absolute top-0 left-0 flex-grow flex-shrink">
    <div x-show="visible" class="absolute z-10 w-full max-w-full flex flex-row">
        <input type="text" x-ref="search_box" x-model="textval"
        @change.prevent.stop="if (textval.length > 0) {$dispatch('spotsearch', { {{$textname}}: textval});}"
        @input.prevent.stop=""
        @keyup.prevent.stop="if($event.code == 'Escape') {
            textval='';
            visible=false; $dispatch('spotsearch', { {{$textname}}: textval});
        }"
        @click.outside="visible=textval.length > 0;"
        placeholder="{{$label}}" class="input input-sm input-bordered text-accent w-full"/>
        <button type="button"
        @click.prevent.stop="
        if (textval=='') {
            visible=false;
        } else {
            textval='';
            visible=false; $dispatch('spotsearch', { {{$textname}}: textval});
        }"
        class="bg-error border-none rounded-tr-lg rounded-bl-sm inline-block h-4 w-4 p-0 absolute top-0 right-0"><x-easyadmin::display.icon icon="icons.close" height="h-3" width="w-3"/></button>
    </div>
    <button type="button" class="btn btn-sm bg-inherit border-none focus:outline-primary-focus text-base-content hover:bg-base-100" @click.prevent.stop="visible=true; $nextTick(() => $refs.search_box.focus());">
        <x-easyadmin::display.icon icon="icons.search" height="h-6" width="w-6"/>
    </button>
</div>
