@props(['inputId'])
<div x-data="{
    macthes : []
}" class=" flex-col flex relative">
    <label for="" class="font-medium text-base-content">Campaign/Department :</label>
    <input @input="matches = campaignInputChanged($el.value);" type="text" required name="campaign" id="{{$inputId}}" class="input input-bordered input-secondary md:w-96 focus:outline-none min-w-72 ">

    {{-- autocomplete area --}}
    <div x-show="autoComplete" @click.outside="autoComplete = false" class="absolute md:w-96 min-w-72 bg-base-300 p-2 top-[4.5rem] flex flex-col space-y-0.5 rounded-lg h-fit max-h-40 overflow-y-scroll hide-scroll">
        <template x-for="match in matches">
            <button @click.prevent.stop="completeInput(match, '{{$inputId}}');" x-text="match" class=" py-1.5 rounded-md text-start w-full hover:bg-base-200 px-1"></button>
        </template>
    </div>

</div>
