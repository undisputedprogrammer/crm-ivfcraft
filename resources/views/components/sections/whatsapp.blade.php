@props(['templates'])
<div x-show="!messageLoading" class=" h-[470px] hide-scroll relative">
    <div x-data="{
        open: false,
    }" class=" overflow-y-scroll h-[calc(470px-48px)] hide-scroll"
    @notify.window="
    if(lead.id == $event.detail.lead_id){
        chats.push($event.detail.msg);
    }">
        <template x-if="chats.length != 0">
            <template x-for="chat in chats">

                    <div class="chat " :class = "chat.direction == 'Inbound' ? ' chat-start' : ' chat-end' ">

                        <template x-if="chat.type == 'text'">
                            <div class="chat-bubble font-medium" :class = "chat.direction == 'Outbound' ? ' chat-bubble-success' : '' " x-text="chat.message"></div>
                        </template>

                        <template x-if="chat.type == 'media'">
                            <div class="chat-bubble font-medium" :class = "chat.direction == 'Outbound' ? ' chat-bubble-success' : '' " >
                                <img :src="chat.message" class=" rounded-lg w-44 h-fit" alt="">
                            </div>
                        </template>

                    </div>

            </template>
        </template>

        <template x-if = "chats.length == 0">
            <div class=" py-8 w-full flex justify-center">
                <label for="" class=" text-center font-medium text-base">Start the chat with a template</label>
            </div>
        </template>
    </div>


    <div class=" absolute bottom-0 w-full pt-1 z-40">
        <form x-data="{
            value : '',
            custom : false,
            media_selected: false,
            filename: '',
            media_uploader: document.getElementById('media-uploader'),
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
            console.log($event.detail.content);

            if($event.detail.content.status == 'success'){
                chats.push($event.detail.content.chat);
                $dispatch('showtoast', {message: 'Message sent successfully', mode: 'success'});
                $el.reset();
                custom = false;
                value = '';
                resetMedia();
            }
            else if ($event.detail.content.status == 'fail') {
                $dispatch('showtoast', {message: $event.detail.content.errors, mode: 'error'});

            }
            else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }

        }"
         class="flex justify-evenly pt-2 space-x-1" id="wp-message-form" action="{{route('message.sent')}}" method="POST" @submit.prevent.stop="doSubmit()" x-show="lead.id != null">
        @csrf

            <button @click.prevent.stop="media_uploader.click();" x-show="custom_enabled" class="btn btn-ghost bg-base-200">
                <x-icons.attach-icon/>
            </button>

            <input @change="setMedia();" type="file" id="media-uploader" name="media" class="hidden">

            <label x-show="media_selected" for="" class=" input min-w-[74%] flex space-x-2 justify-between  bg-white items-center" >
                <p  x-text="'Selected : '+filename" class="text-sm font-medium"></p>
                <button @click.prevent.stop="resetMedia();" class="btn btn-ghost btn-sm">
                    <x-icons.close-icon/>
                </button>
            </label>

            <select @change.prevent.stop="validate()" :required="!custom && !media_selected ? true : false " x-model="value" x-show="!custom && !media_selected" name="template" id="select-template" class=" select select-info min-w-[70%] focus:ring-0 focus:outline-none" >
                <option value="" disabled selected>-- Select template  --</option>
                @foreach ($templates as $template)
                    <option value="{{$template->id}}">{{$template->template}}</option>
                @endforeach
                <option x-show="custom_enabled" value="custom">Custom message</option>

            </select>

            <button @click.prevent.stop="custom = false;
            value = ''" x-show="custom && !media_selected" class="btn text-primary"
            @resetselect.window="
            custom = false;
            value = '';
            ">
                T
            </button>

            <input x-show="custom && !media_selected" name="message" :required="custom ? true : false " type="text" placeholder="Type message and press sent" class="input input-info bg-white w-[74%]  focus:ring-0 focus:outline-none text-black font-medium">

            <button type="submit" class="btn btn-success">
                <x-icons.send-icon/>
            </button>
        </form>
    </div>

</div>

