<x-easyadmin::app-layout>

    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div class=" flex flex-col justify-evenly items-start w-full bg-base-200 pt-7 pl-[3.3%] space-x-2">

        <h1 class=" font-bold text-primary text-lg w-full">Sources</h1>

        <div class=" flex flex-col lg:flex-row items-start w-full  space-y-3 lg:space-y-0 lg:space-x-8 mt-4">

            <div class=" rounded-lg  border-secondary border overflow-x-auto w-[40%]">
                <table class="table table-sm  rounded-lg">
                    <!-- head -->
                    <thead class="rounded-t-lg">
                        <tr class=" text-secondary  sticky top-0 bg-base-300">
                            <th>Code</th>
                            <th>Name</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sources as $source)
                            <tr class="text-base-content hover:bg-base-100 cursor-pointer">


                                <th>{{ $source->code }}</th>
                                <td>{{$source->name}}</td>
                                <td>{{ $source->created_at->format('d M Y') }}</td>
                                <td></td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>
        </div>

        <div class="p-6 rounded-lg bg-base-100">
            <h1 class=" font-medium text-base text-primary">Create new source</h1>

            <form x-data="{
                doSubmit(){
                    let form = document.getElementById('source-create-form');
                    let formdata = new FormData(form);
                    $dispatch('formsubmit',{url:'{{route('source.store')}}', route: 'source.store',fragment: 'page-content', formData: formdata, target: 'source-create-form'});
                }
            }"
            @formresponse.window="
            if($event.detail.target == $el.id){
                if ($event.detail.content.success) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                    $dispatch('linkaction',{link: '{{route('sources.index')}}', route: 'sources.index', fragment: 'page-content', fresh: true});
                } else if (typeof $event.detail.content.errors != undefined) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                } else{
                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                }
            }" @submit.prevent.stop="doSubmit();" id="source-create-form" class=" flex flex-col space-y-3 mt-2.5">
                <input type="text" name="code" required class="input input-bordered text-base-content focus:outline-none min-w-72" placeholder="Enter source short code">
                <input type="text" name="name" required class="input input-bordered text-base-content focus:outline-none min-w-72" placeholder="Enter source name">
                <button type="submit" class=" btn btn-primary w-fit">Save</button>
            </form>

        </div>


        </div>




      </div>
    </div>
</x-easyadmin::app-layout>
