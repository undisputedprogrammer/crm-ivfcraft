@props(['templates'])
{{-- messenger --}}
<div x-data="{
    showMessageForm : false,
    showForm(){
        this.showMessageForm = true;
    },
    custom: false,
    selectedTemplate: '',
    evaluateTemplate(){

        if(this.selectedTemplate == 'custom'){
            this.custom = true;
        }
        else{
            this.custom = false;
        }
    },
    collapse(){
        this.selectedTemplate = '';
        this.custom = false;
        this.showMessageForm = false;
    }
}" class=" my-1">

    <button @click.prevent.stop="showForm()" x-show="!showMessageForm" x-transition class=" btn btn-success btn-sm">
        <x-icons.whatsapp-icon/>
        Message
    </button>

    <h1 x-show="showMessageForm" class=" text-secondary font-medium text-base mb-1">Send Message</h1>
    <form
    x-data = "{
        doSubmit() {
            let form = document.getElementById('message-form');
            let formdata = new FormData(form);

            formdata.append('lead_name',lead.name);
            $dispatch('formsubmit',{url:'{{route('message.sent')}}', route: 'message.sent',fragment: 'page-content', formData: formdata, target: 'message-form'});
        }
    }"
    id="message-form"
     x-show="showMessageForm" x-transition action="" class=" bg-base-200 p-3 rounded-xl max-w-sm flex flex-col space-y-3"
     @submit.prevent.stop="doSubmit()"
     @formresponse.window="
        console.log($event.detail.content);
        if ($event.detail.target == $el.id) {
            if ($event.detail.content.success) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                $el.reset();
                collapse();
                $dispatch('formerrors', {errors: []});
            } else if (typeof $event.detail.content.errors != undefined) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

            } else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }
        }"
     >

        <select
        @change="
            selectedTemplate = $el.value;
            evaluateTemplate();"
        class="select select-bordered w-full" required>
            <option value="" disabled selected>Select message template</option>

            @foreach ($templates as $template)
                <option value="{{$template->id}}">{{$template->template}}</option>
            @endforeach

            <option value="custom">Sent custom message</option>

        </select>

        <textarea x-show="custom" :required = " custom ? true : false " placeholder="What is your message ?" name="message" class="textarea textarea-bordered textarea-sm w-full max-w-sm"></textarea>

        <div class=" flex space-x-3">
            <button type="submit" class=" btn btn-secondary btn-sm ">Sent</button>
            <button @click.prevent.stop="collapse();" class="btn btn-error btn-sm">Close</button>
        </div>

    </form>

</div>
{{-- messenger ends --}}
