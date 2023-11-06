<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <h2 class="py-4 px-12 text-lg font-semibold text-primary bg-base-200">Manage Messages</h2>


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

       class=" h-[calc(100vh-3.5rem)] pt-7 pb-3  bg-base-200 w-full flex justify-evenly">


        <x-tables.messages-table :messages="$messages"/>



        <div
            x-data="{
                mode: 'add',
            }"

            class="w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div x-show="mode=='add'" x-transition>
                <h2 class="text-lg font-semibold text-secondary ">Add Message</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="message-add-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('message-add-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('messages.store')}}', formData: fd, target: 'message-add-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        console.log($event.detail);
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Message Added!'});$dispatch('linkaction', {link: '{{route('messages.index')}}', route: 'messages.index', fragment: 'page-content'});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add message. Please make sure you have entered all details.'});
                            }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Template</span>
                            </label>
                            <input type="text" name="template" placeholder="Template name" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Message</span>
                            </label>
                            <input type="text" name="message" placeholder="Message" class="input input-bordered w-full max-w-xs" />
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
                    template: '',
                    message: '',
                    reset() {
                        this.id = '';
                        this.template = '';
                        this.message = '';
                        mode = 'add';
                    }
                }"
                x-show="mode=='edit'"
                @editmessage.window="
                    console.log($event.detail);
                    id = $event.detail.id;
                    template = $event.detail.template;
                    message = $event.detail.message;
                    mode='edit';
                "  x-transition>

                <h2 class="text-lg font-semibold text-primary ">Edit Message</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="message-edit-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('message-edit-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('messages.update', '_X_')}}'.replace('_X_', id), formData: fd, target: 'message-edit-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Message Updated!'});
                                let params = {
                                    page: page
                                };
                                $dispatch('linkaction', {link: '{{route('messages.index')}}', route: 'messages.index', params: params, fresh: true});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to edit message. Please make sure you have entered all details.'});
                            }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Template</span>
                            </label>
                            <input type="text" name="template" x-model="template" placeholder="Template name" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Message</span>
                            </label>
                            <input type="text" name="message" x-model="message" placeholder="Message" class="input input-bordered w-full max-w-xs" />
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
