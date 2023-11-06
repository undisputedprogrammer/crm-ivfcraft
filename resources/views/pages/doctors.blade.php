<x-easyadmin::app-layout>
<div x-init="
        selectedCenter = null;
        @isset($selectedCenter)
            selectedCenter = {{$selectedCenter}};
        @endisset">
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}

      <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
        <h2 class=" text-lg font-semibold text-primary bg-base-200">Manage Doctors</h2>
        <div>
            @can('is-admin')
                @php
                $route = "doctors.index";
                @endphp
                <x-forms.filter-leads :route="$route" :centers="$centers"/>
            @endcan
        </div>
      </div>



      <div x-data="{page: 0}"
        x-init="
            page = {{request()->input('page', 0)}};
        "

        {{-- pagination event handler --}}
        @pageaction.window="
            page = $event.detail.page;
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
            })"

       class=" md:h-[calc(100vh-3.5rem)] pt-7 pb-12 md:pb-0  bg-base-200 w-full flex flex-col md:flex-row justify-evenly items-center md:items-start space-y-4 md:space-y-0">


        <x-tables.doctors-table :doctors="$doctors"/>



        <div
            x-data="{
                mode: 'add',
            }"
            class=" w-[96%] md:w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div x-show="mode=='add'" x-transition>
                <h2 class="text-lg font-semibold text-secondary ">Add Doctor</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="doctor-add-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('doctor-add-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('doctors.store')}}', formData: fd, target: 'doctor-add-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($event.detail.target == $el.id){
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Doctor Added!'});$dispatch('linkaction', {link: '{{route('doctors.index')}}', route: 'doctors.index', fragment: 'page-content'});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add doctor. Please make sure you have entered all details.'});
                            }
                        }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Name</span>
                            </label>
                            <input type="text" name="name" placeholder="Name" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Department</span>
                            </label>
                            <input type="text" name="department" placeholder="Department" class="input input-bordered w-full max-w-xs" />
                        </div>

                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Select Center</span>
                            </label>
                            <select name="center_id" id="agent-center" required class=" select text-base-content w-full max-w-xs select-bordered">
                                <option value="" disabled selected>-- choose center --</option>
                                @foreach ($centers as $center)
                                    <option value="{{$center->id}}">{{$center->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
            <div
                x-data="{
                    id: '',
                    name: '',
                    department: '',
                    center_id: '',
                    reset() {
                        this.id = '';
                        this.name = '';
                        this.department = '';
                        this.center_id = '';
                        mode = 'add';
                    }
                }"
                x-show="mode=='edit'"
                @doctoredit.window="
                    id = $event.detail.id;
                    name = $event.detail.name;
                    department = $event.detail.department;
                    center_id = $event.detail.center_id;
                    mode='edit';
                "  x-transition>
                <h2 class="text-lg font-semibold text-primary ">Edit Doctor</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="doctor-edit-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('doctor-edit-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('doctors.update', '_X_')}}'.replace('_X_', id), formData: fd, target: 'doctor-edit-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($event.detail.target == $el.id){
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Doctor Updated!'});
                                let params = {
                                    page: page
                                };
                                $dispatch('linkaction', {link: '{{route('doctors.index')}}', route: 'doctors.index', params: params, fresh: true, fragment: 'page-content'});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add doctor. Please make sure you have entered all details.'});
                            }
                        }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Name</span>
                            </label>
                            <input type="text" name="name" x-model="name" placeholder="Name" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Department</span>
                            </label>
                            <input type="text" name="department" x-model="department" placeholder="Department" class="input input-bordered w-full max-w-xs" />
                        </div>

                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Select Center</span>
                            </label>
                            <select name="center_id" id="agent-center" required class=" select text-base-content w-full max-w-xs select-bordered">
                                {{-- <option value="" disabled>-- choose center --</option> --}}
                                @foreach ($centers as $center)
                                    <option :selected="center_id == '{{$center->id}}' " value="{{$center->id}}">{{$center->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-secondary bg-secondary">Update</button><br/><br/>
                            <button @click.prevent.stop="reset();" type="button" class="btn btn-ghost btn-xs">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
  <x-footer/>
</x-easyadmin::app-layout>
