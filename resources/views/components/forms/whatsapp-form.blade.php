@props(['templates'])
<div class=" absolute bottom-0 w-full pt-1 z-40">
    <form x-data="{
        value : '',
        custom : false,
        media : false,
        media_uploader : document.getElementById('media-uploader'),
        media_selected : false,
        filename : '',
        validate(){
            if(this.value == 'custom'){
                this.custom = true;
            }
        },
        doSubmit(){
            let form = document.getElementById('wp-message-form');
            let formdata = new FormData(form);
            formdata.append('lead_id',lead.id);
            $dispatch('formsubmit',{url:'{{route('message.sent')}}', route: 'message.sent',fragment: 'page-content', formData: formdata, target: 'wp-message-form'});
        },
        resetMedia(){
            this.media_selected = false;
            this.filename = '';
            $el.reset();
        },
        setMedia(){
            this.media_selected = true;
            this.filename = this.media_uploader.files[0].name;
        }

    }"
    @formresponse.window="
    if($el.id == $event.detail.target){
        console.log($event.detail.content.message);

        if($event.detail.content.status == 'success'){

                chats.push($event.detail.content.chat);
                allChats[lead.id] = chats;

            $dispatch('showtoast', {message: 'Message sent successfully', mode: 'success'});
            $el.reset();
            resetMedia();
        }
        else if ($event.detail.content.status == 'fail') {
            $dispatch('showtoast', {message: $event.detail.content.errors, mode: 'error'});

        }
        else{
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        }

    }"
     class="flex justify-center space-x-3 py-3" id="wp-message-form" action="{{route('message.sent')}}" method="POST" @submit.prevent.stop="doSubmit()">
    @csrf

        {{-- media components --}}
        <button x-show="freehand_enabled" class="btn bg-base-200"
        @click.prevent.stop="media_uploader.click();">
            <x-icons.attach-icon/>
        </button>

        <input type="file" name="media" id="media-uploader" class="hidden"
        @change="setMedia();">

        <label x-show="media_selected" for="" class=" input min-w-[74%] flex space-x-2 justify-between  bg-white items-center" >
            <p  x-text="'Selected : '+filename" class="text-sm font-medium"></p>
            <button @click.prevent.stop="resetMedia();" class="btn btn-ghost btn-sm">
                <x-icons.close-icon/>
            </button>
        </label>

        {{-- media components ends --}}

        <select @change.prevent.stop="validate()" :required="!custom && !media_selected? true : false " x-model="value" x-show="!custom && !media_selected" name="template" id="select-template" class=" select select-info bg-white w-[74%] focus:ring-0 focus:outline-none" >
            <option value="" disabled selected>-- Select template  --</option>
            @foreach ($templates as $template)
                <option value="{{$template->id}}">{{$template->template}}</option>
            @endforeach
            <option x-show="freehand_enabled" value="custom">Custom message</option>
        </select>

        <button @click.prevent.stop="custom = false;
        value = ''" x-show="custom && !media_selected" class="btn text-primary">
            T
        </button>

        <input x-show="custom && !media_selected" name="message" :required="custom && !media_selected ? true : false " type="text" placeholder="Type message and press sent" class="input input-info bg-white w-[74%]  focus:ring-0 focus:outline-none text-black font-medium">

        <button type="submit" class="btn btn-success">
            <x-icons.send-icon/>
        </button>
    </form>
</div>
