<x-easyadmin::app-layout>
    <div>
        <div x-data="x_messenger"
        @newmessage.window="console.log($event.detail);"
         class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black "
        x-init="
        @if (isset($latest))
            console.log('{{$latest->id}}');
            if(latest == null){
            latest = {{$latest->id}};
            }
            $dispatch('messengerlatest', {latest: latest});
        @endif
        @if (isset($leads))
            allLeads = {{$leads}};
        @endif

        ">


            <x-sections.side-drawer />
            {{-- page body --}}



            <div x-data="{
                selected: null,
                lead : null,
                chats: [],
                loadingChats: false,
                thelink: '',
                expiration_time: null,
                freehand_enabled: false,
                showChat(lead) {
                    this.lead = lead;
                    console.log('loading chats of'+this.lead.name);
                    this.loadingChats = true;
                    this.selected = lead.id;
                    this.markasread(lead.id);
                    setTimeout(() => {
                        this.chats = allChats[lead.id];
                        this.loadingChats = false;
                        this.expiration_time = this.getExpiry(this.chats);
                        this.checkExpiry(this.expiration_time);
                      }, '1000');

                },
                theLeads(){
                    return allLeads.filter((l) => {
                        return l != null;
                    });
                },
                markasread(lead_id){
                    if(allChats[lead_id].filter(chat => chat.status == 'received').length > 0){
                        axios.get('/mark/read',{
                            params:{
                                lead_id : lead_id
                            }
                        }).then((r)=>{
                            if(r.data == true){
                                allChats[lead_id].forEach(chat => {
                                    if (chat.lead_id == lead_id && chat.direction == 'Inbound') {
                                        chat.status = 'read';
                                    }
                                });
                            }
                        }).catch((c)=>{
                            console.log('Could not mark messages as read');
                        })
                    }
                },
                getExpiry(messages){
                    let lastInboundMessage = null;

                    for (let i = messages.length - 1; i >= 0; i--) {
                        if (messages[i].direction == 'Inbound') {
                            lastInboundMessage = messages[i];
                            break;
                        }
                    }
                    return lastInboundMessage.expiration_time;
                },
                checkExpiry(timestamp){
                    if(timestamp == null){
                        this.freehand_enabled = false;
                    }
                    else{
                        const date = new Date(timestamp * 1000);
                        const options = {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        timeZone: 'Asia/Kolkata',
                        };

                        const formattedDate = new Intl.DateTimeFormat('en-IN', options).format(date);
                        console.log(formattedDate);
                        const currentDate = new Date();
                        const timeDifference = currentDate - date;
                        const twentyFourHoursInMillis = 24 * 60 * 60 * 1000;

                        if (timeDifference >= twentyFourHoursInMillis) {
                            this.freehand_enabled = false;
                        } else {
                            this.freehand_enabled = true;
                        }
                    }
                }
            }"
            @appendChat.window="console.log('event captured');"

             class="h-[calc(100vh-3.5rem)] bg-base-200 w-full flex justify-evenly"
             x-init="thelink='{{route('leads.show', '_X_')}}';">

                {{-- body starts here --}}

                <div class="  overflow-auto basis-[20%] hide-scroll my-2" x-init="">

                    <template x-for="l in theLeads()">
                        <div @click.prevent.stop="
                        showChat(l);"
                            class=" flex items-start  cursor-pointer hover:bg-base-100 rounded-xl my-1.5 w-full"
                            :class = "selected == l.id ? ' bg-base-100' : '' "
                            x-init = "
                            if(l.chats != null && l.chats != undefined){
                                allChats[l.id] = l.chats
                            }
                            ">

                            <div x-data="{
                                length : 0,
                                message : ''
                            }" class=" px-3 text-base-content py-4 w-full" x-init="length = allChats[l.id].length;">
                                <div class="flex items-bottom justify-between w-full">
                                    <p class=" text-base font-medium" x-text="l.name">

                                    </p>
                                    <p class="text-xs" x-text="convertTime(allChats[l.id][allChats[l.id].length -1].created_at)">

                                    </p>
                                </div>
                                <div class="flex justify-between">
                                    <p  class=" mt-1 text-sm line-clamp-1" x-text=" allChats[l.id][allChats[l.id].length - 1].type == 'text' ?  allChats[l.id][allChats[l.id].length - 1].message : 'photo' " >
                                    </p>
                                    <span x-show="allChats[l.id].filter(chat => chat.status == 'received').length != 0" class=" rounded-full bg-green-600 text-base-content font-medium aspect-square h-6 text-center" x-text="allChats[l.id].filter(chat => chat.status == 'received').length"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>


                {{-- Messages section --}}


                <div class="flex justify-evenly basis-[76%] rounded-xl my-2  overflow-hidden"
                    style="background-color: #DAD3CC">

                    <div class="py-2 px-3 basis-[65%]   hide-scroll relative flex">


                            <p x-show="lead == null" class="font-semibold text-lg text-center py-2.5 w-full" x-text="lead == null ? 'Select a lead' : '' "></p>


                        <template x-if="chats.length > 0">
                            <div class="overflow-auto w-full hide-scroll mb-[72px]">
                                <template x-for="chat in chats">
                                    <div class="chat " :class="chat.direction == 'Inbound' ? ' chat-start' : ' chat-end'">
                                        {{-- displaying text message --}}
                                        <template x-if="chat.type == 'text' ">
                                            <div class="chat-bubble text-base-content font-medium" x-text="chat.message"
                                                :class="chat.direction == 'Inbound' ? ' bg-base-100' : ' bg-[#128c7e] text-white'">
                                            </div>
                                        </template>
                                        {{-- displaying media message --}}
                                        <template x-if="chat.type == 'media' ">
                                            <div class="chat-bubble text-base-content font-medium"
                                                :class="chat.direction == 'Inbound' ? ' bg-base-100' : ' bg-[#128c7e] text-white'">
                                                <img :src="chat.message" alt="" class="w-52 h-fit rounded-lg">
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                        </template>

                        <template x-if="lead != null">
                            <x-forms.whatsapp-form :templates="$templates"/>
                        </template>


                    </div>

                    {{-- loading animation --}}
                    <div x-show="loadingChats" class=" h-screen w-full flex justify-center items-center bg-black absolute z-40 top-0 left-0 opacity-40">

                        <span class="loading loading-ball loading-xs bg-primary"></span>
                        <span class="loading loading-ball loading-sm bg-primary"></span>
                        <span class="loading loading-ball loading-md bg-primary"></span>
                        <span class="loading loading-ball loading-lg bg-primary"></span>

                    </div>

                    <div class="basis-[30%] rounded-xl bg-base-100 my-2 text-base-content">
                        <h1 class=" font-semibold text-center text-lg w-full py-2 text-primary" x-text=" lead != null ? 'Lead details' : 'Select a lead' ">Lead details</h1>

                        <template x-if="lead != null && !loadingChats">
                            <div class=" flex flex-col space-y-2 p-3 transition-all">

                                <p class=" font-medium text-base">Name : <span x-text="lead.name"></span></p>

                                <p class=" font-medium text-base">City : <span x-text="lead.city"></span></p>

                                <p class=" font-medium text-base">Phone : <span x-text="lead.phone"></span></p>

                                <p class=" font-medium text-base">Email : <span x-text="lead.email"></span></p>

                                <button @click.prevent.stop="$dispatch('linkaction',{link:thelink.replace('_X_', lead.id),route: 'leads.show',fragment:'page-content'});" type="button" class=" btn btn-ghost border border-secondary w-fit btn-sm text-secondary self-center">View Lead</button>

                            </div>
                        </template>
                    </div>



                </div>

                {{-- body ends here --}}
            </div>

        </div>

    </div>
</x-easyadmin::app-layout>
