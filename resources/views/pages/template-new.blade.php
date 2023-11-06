<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <h2 class="py-4 px-12 text-lg font-semibold text-primary bg-base-200">Manage Templates</h2>


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


        {{-- <x-tables.messages-table :messages="$messages"/> --}}



        <div
            x-data="{
                mode: 'add',
            }"

            class="w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div x-show="mode=='add'" x-transition>
                <h2 class="text-lg font-semibold text-secondary ">Add Message</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="template-add-form"
                        x-data="{
                            showerror : false,
                            variableCount : 0,
                            variable_appender : document.getElementById('variable-appender'),
                            doSubmit() {
                                let form = document.getElementById('template-add-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('template.store')}}', formData: fd, target: 'template-add-form'});
                            },
                            addInput() {
                                if(this.variableCount != 0 && document.getElementById('var-'+this.variableCount).value == '' && document.getElementById('data-'+this.variableCount).value == ''){
                                    this.showerror = true;
                                    setTimeout(() => {
                                        this.showerror = false;
                                      }, '2000');
                                }
                                else{
                                    this.variableCount++;


                                    let element = document.createElement('input');
                                    let elementID = 'var-'+this.variableCount;
                                    let elementName = 'var_'+this.variableCount;
                                    element.classList = 'input input-bordered w-[45%] max-w-xs mt-2';
                                    element.placeholder = 'Enter variable name';
                                    element.id = elementID;
                                    element.name = elementName;
                                    element.required = true;

                                    let elementdata = document.createElement('input');
                                    let elementdataID = 'data-'+this.variableCount;
                                    let elementdataName = 'data_'+this.variableCount;
                                    elementdata.classList ='input input-bordered w-1/2 max-w-xs mt-2';
                                    elementdata.placeholder = 'Enter data reference';
                                    elementdata.id = elementdataID;
                                    elementdata.name = elementdataName;
                                    elementdata.required = true;

                                    let div = document.createElement('div');
                                    div.classList = 'flex justify-between';
                                    div.id = 'div-'+this.variableCount;
                                    div.append(element);
                                    div.append(elementdata);
                                    this.variable_appender.append(div);
                                }

                            },
                            removeInput() {
                                let inputToRemove = document.getElementById('div-'+this.variableCount);
                                this.variable_appender.removeChild(inputToRemove);
                                this.variableCount--;
                            }
                        }"
                        class="flex flex-col items-center w-full "
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($event.detail.target == $el.id){
                        console.log($event.detail);
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Template Created!'});
                                console.log($event.detail.content.params);
                                $el.reset();
                                variableCount = 0;
                                {{-- $dispatch('linkaction', {link: '{{route('messages.index')}}', route: 'messages.index'}); --}}
                                while (variable_appender.firstChild) {
                                    variable_appender.removeChild(variable_appender.firstChild);
                                }
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add message. Please make sure you have entered all details.'});
                            }
                        }
                        "
                        >

                        <div class="form-control w-full max-w-sm">
                            <label class="label">
                            <span class="label-text font-medium">Template Name</span>
                            </label>
                            <input type="text" name="template" placeholder="Template name" class="input input-bordered w-full max-w-sm" />
                        </div>

                        <div class="form-control w-full max-w-sm">
                            <label class="label">
                            <span class="label-text font-medium">Template Body</span>
                            </label>
                            <textarea name="templatebody" class=" textarea textarea-bordered w-full max-w-sm" id="" required></textarea>
                        </div>

                        {{-- variable appender --}}
                        <div id="variable-appender" class="form-control w-full max-w-sm flex flex-col space-y-2">

                        </div>
                        <button @click.prevent.stop="removeInput()" x-show="variableCount > 0" class=" btn btn-link btn-xs text-error">Remove</button>

                        <label for="" class="mt-1.5"><span x-show="showerror" x-transition class=" text-error text-sm">Fill the current input before adding another</span></label>

                        <div class="text-center py-2 flex space-x-2">

                            <button @click.prevent.stop="addInput()" class="btn btn-sm btn-secondary">Add new variable</button>

                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- **************************************************************************************
            *                      create form ends                                              *
            ************************************************************************************** --}}



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
                            {{-- doSubmit() {
                                let form = document.getElementById('message-edit-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('messages.update', '_X_')}}'.replace('_X_', id), formData: fd, target: 'message-edit-form'});
                            } --}}
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($event.detail.target == $el.id){
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Message Updated!'});
                                let params = {
                                    page: page
                                };
                                $dispatch('linkaction', {link: '{{route('template.index')}}', route: 'template.index', params: params, fresh: true});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to edit message. Please make sure you have entered all details.'});
                            }
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
