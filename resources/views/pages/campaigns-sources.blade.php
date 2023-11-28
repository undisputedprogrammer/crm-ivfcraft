<x-easyadmin::app-layout>

    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div x-data="{
        currentForm : 'create',
        selectedSource : {},
        toggleEdit(source){
            this.selectedSource = JSON.parse(source);
            this.currentForm = 'edit';
        },
        resetEdit(){
            this.currentForm = 'create';
            this.selectedSource = {};
        }
      }"

       class=" flex flex-col justify-evenly items-start w-full bg-base-200 pt-7 pl-[3.3%] space-x-2">

        <h1 class=" font-bold text-primary text-lg w-full">Sources</h1>

        <div class=" flex flex-col lg:flex-row items-start w-full  space-y-3 lg:space-y-0 lg:space-x-8 mt-4">

            <div class=" rounded-lg  border-secondary border overflow-x-auto w-[40%]">
                <table class="table table-sm  rounded-lg">
                    <!-- head -->
                    <thead class="rounded-t-lg">
                        <tr class=" text-secondary  sticky top-0 bg-base-300">
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sources as $source)
                            <tr class="text-base-content hover:bg-base-100 cursor-pointer">


                                <th>{{ $source->code }}</th>
                                <td>{{$source->name}}</td>
                                <td
                                    x-text="{{$source->is_enabled}} ? 'Enabled' : 'Disabled'"
                                    class=" font-medium"
                                    :class="{{$source->is_enabled}} ? ' text-success' : ' text-error'">
                                </td>
                                <td>{{ $source->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class=" btn btn-ghost btn-xs" @click="toggleEdit('{{$source}}');">
                                        <x-icons.edit-icon/>
                                    </button>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>
        </div>

        <div
        @toggleEdit.window="
      console.log('event recieved');
      "
         class="p-6 rounded-lg bg-base-100">

            {{-- *************Source create form************** --}}
            <h1 x-show="currentForm == 'create'" class=" font-medium text-base text-primary">Create new source</h1>

            <p class=" text-error text-sm" id="error-displayer"></p>

            <form x-show="currentForm == 'create'" x-data="{
                doSubmit(){
                    let form = document.getElementById('source-create-form');
                    let formdata = new FormData(form);
                    if(formdata.getAll('forms[]').length < 1){
                        document.getElementById('error-displayer').innerText = 'Select atleast one form to include in';
                        setTimeout(()=>{
                            document.getElementById('error-displayer').innerText = '';
                        }, 5000);
                        return false;
                    }
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

                <div class=" flex flex-col space-y-1">
                    <h1 class="font-medium text-base text-base-content">Include in :</h1>
                    @foreach (config('appSettings.forms') as $form)
                        <div class=" flex flex-row space-x-2 items-center">
                            <input type="checkbox" name="forms[]" value="{{$form}}" class=" checkbox checkbox-secondary checkbox-xs ring-0 outline-none">
                            <p class=" text-base-content ">{{$form}}</p>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class=" btn btn-primary w-fit btn-sm">Save</button>

            </form>


            {{-- ***************Source edit form*************** --}}
            <h1 x-show="currentForm == 'edit'" class=" font-medium text-base text-primary">Edit source</h1>
            <form x-show="currentForm == 'edit'" x-data="{
                doSubmit(){
                    if(selectedSource.hasOwnProperty('id') && selectedSource.id != undefined){
                        let form = document.getElementById('source-edit-form');
                        let formdata = new FormData(form);

                        console.log(formdata.getAll('forms[]'));
                        if(formdata.getAll('forms[]').length < 1){
                            document.getElementById('error-displayer').innerText = 'Select atleast one form to include in';
                            setTimeout(()=>{
                                document.getElementById('error-displayer').innerText = '';
                            }, 5000);
                            return false;
                        }
                        if(formdata.get('is_enabled') == 'on'){
                            formdata.set('is_enabled', true);
                        }else{
                            formdata.set('is_enabled', false);
                        }
                        formdata.append('source_id', selectedSource.id);
                        $dispatch('formsubmit',{url:'{{route('source.update')}}', route: 'source.update',fragment: 'page-content', formData: formdata, target: 'source-edit-form'});
                    }
                    else{
                        $dispatch('showtoast', {message: 'Could not find source details, please refresh and try again !', mode: 'error'});
                    }
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
            }" @submit.prevent.stop="doSubmit();" id="source-edit-form" class=" flex flex-col space-y-3 mt-2.5">
                <input type="text" name="code" :value="selectedSource.code != undefined ? selectedSource.code : 'UNKNOWN'" required class="input input-bordered text-base-content focus:outline-none min-w-72" placeholder="Enter source short code">
                <input type="text" name="name" :value="selectedSource.name != undefined ? selectedSource.name : 'UNKNOWN'" required class="input input-bordered text-base-content focus:outline-none min-w-72" placeholder="Enter source name">

                <div class=" flex flex-col space-y-1">
                    <h1 class="font-medium text-base text-base-content">Include in :</h1>
                    @foreach (config('appSettings.forms') as $form)
                        <div class=" flex flex-row space-x-2 items-center">
                            <input type="checkbox" name="forms[]" :checked="selectedSource.forms != undefined && selectedSource.forms.includes('{{$form}}')" value="{{$form}}" class=" checkbox checkbox-secondary checkbox-xs ring-0 outline-none">
                            <p class=" text-base-content ">{{$form}}</p>
                        </div>
                    @endforeach
                </div>

                <div class=" flex flex-row space-x-2">
                    <p class=" font-medium text-base-content">Enable :</p>
                    <input type="checkbox" name="is_enabled" :checked="selectedSource.is_enabled" class=" checkbox checkbox-secondary checkbox-sm">
                </div>

                <div class=" flex flex-row space-x-3">
                    <button type="submit" class=" btn btn-primary w-fit btn-sm">Save</button>
                    <button class=" btn btn-ghost btn-sm text-error" @click.prevent.stop="resetEdit();">
                        Cancel<x-icons.close-icon/>
                    </button>
                </div>

            </form>

        </div>


        </div>




      </div>
    </div>
</x-easyadmin::app-layout>
