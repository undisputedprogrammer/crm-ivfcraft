<x-easyadmin::app-layout>
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100 text-black">

        <x-sections.side-drawer />
        {{-- page body --}}
{{-- {{dd($chatRooms[0]->name)}} --}}
        <div class=" flex items-center space-x-2 py-4 px-12 bg-base-200">
            <h2 class=" text-lg font-semibold text-primary bg-base-200">Chat Corner</h2>
        </div>
        {{-- Chat Panel --}}
        <div class="flex flex-row space-x-2 h-[calc(100vh-145px)] overflow-hidden bg-base-300 border border-base-300 p-1 rounded-md"
            x-data="{
                user: null,
                chatRooms: [],
                chats: [],
                chatFriends: [],
                dormantFriends: [],
                activeChatRoomId: null,
                activeChatRoom: null,
                activeChats: [],
                lastid: 0,
                oldLoadingChatRoomId: null,
                setDormantFriends() {
                    let cr_uids = this.chatRooms.filter((cr) => {
                        return cr.type == 'one-to-one';
                    }).map((cr) => {
                        return cr.peer.id
                    });
                    this.dormantFriends = this.chatFriends.filter((cf) => {
                        return !cr_uids.includes(cf.id);
                    });
                },
                getChatRoom(entity_id = null, entity_type = null, chat_room_id = null, set_active = true) {
                    axios.get(
                        '{{route('internal_chat.get_chat_room')}}',
                        {params: {
                            entity_id: entity_id,
                            entity_type: entity_type,
                            chat_room_id: chat_room_id
                        }}
                    ).then((r) => {
                        this.chatRooms.push(r.data.chatRoom);
                        if (set_active) {this.setActiveChat(r.data.chatRoom.id);}
                    }).catch((e) => {console.log(e);});
                },
                removeChatRoom(id) {
                    this.chatRooms = this.chatRooms.filter((cr) => {
                        return cr.id != id;
                    });
                },
                setActiveChat(id = null, scroll = 'bottom') {
                    this.activeChatRoomId = id != null ? id : this.chatRooms[0].id;
                    this.activeChatRoom = this.chatRooms.filter((cr) => {
                        return cr.id == this.activeChatRoomId;
                    })[0];
                    this.activeChats = this.activeChatRoom.ordered_chats;
                    $nextTick(() => {
                        $dispatch('activechatupdated', {scrollto: scroll});
                    });
                },
                loadOlderMessages(cid) {
                    let cr = this.chatRooms.filter((c) => {
                        return c.id == cid;
                    })[0];
                    let earliestMsgId = cr.ordered_chats[0].id;
                    axios.get(
                        '{{route('internal_chat.older_messages')}}',
                        {params: {
                            room_id: cid,
                            earliest_msg_id: earliestMsgId,
                        }}
                    ).then((r) => {
                        let crx = this.chatRooms.filter((c) => {
                            return c.id == cid;
                        })[0];
                        crx.ordered_chats = r.data.messages.concat(crx.ordered_chats);
                        if (this.activeChatRoom.id == cid) {
                            this.activeChatRoom.ordered_chats = crx.ordered_chats;
                        }

                        if (r.data.messages.length < {{config('chatSettings.previous_load_count')}}) {

                            crx.noMoreMsgs = true;
                            if (this.activeChatRoom.id == cid) {
                                this.activeChatRoom.noMoreMsgs = true;
                            }
                        }
                        this.setActiveChat(cid, 'top');
                        {{-- cr.ordered_chats =  --}}
                    }).catch((e) => { console.log(e); });
                }
            }"
            x-init="
                unread_ic_count = 0;
                chatRooms = {{Js::from($chatRooms)}};
                chatFriends = {{Js::from($chatFriends)}};

                user = {{Js::from($user)}};
                xmsgs = {{Js::from($loadedChats)}};

                setActiveChat();
                setDormantFriends();
                $watch('chatRooms', (v) => {
                    setDormantFriends();
                });
            "
            @internalchats.window="
                data = $event.detail;

                if (data.data.lastid > lastid) {
                    data.data.messages.forEach((m) => {
                        crx = chatRooms.filter((cr) => {
                            return cr.id == m.chat_room_id;
                        })[0];
                        if (crx) {
                            mids = crx.ordered_chats.map((ic) => {
                                return ic.id;
                            });
                            if (!mids.includes(m.id)) {
                                crx.ordered_chats.push(m);
                            }
                            if (crx.id == activeChatRoomId) {
                                $dispatch('activechatupdated', {scrollto: 'bottom'});
                            }
                        } else {
                            if (lastid != 0) {
                                getChatRoom(null, null, m.chat_room_id, false);
                            }
                        }

                    });
                }
                lastid = data.data.lastid;
            "
            >
            <div class="flex flex-col bg-base-200 p-1 rounded-md w-52 max-h-full">
                <div class="py-2 pl-4 opacity-70 border border-base-content border-opacity-20 rounded-md flex flex-row justify-between bg-base-300 font-bold text-warning mb-2">
                    <span>Chats</span>
                </div>
                <ul class="max-h-full overflow-y-scroll w-full">
                    <template x-for="c in chatRooms">
                        <li @click.prevent.stop="setActiveChat(c.id);" class="py-2 pl-4 text-base-content opacity-70 border border-base-content border-opacity-20 rounded-md flex flex-row justify-between mb-2" :class="c.id != activeChatRoomId || 'bg-base-300'">
                            <span x-text="c.name"></span>
                            <button x-show="c.type != 'public'" @click.prevent.stop="removeChatRoom(c.id);" type="button" class="btn btn-xs text-error">
                                <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-3" width="w-3"/>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
            <div class="flex flex-col flex-grow h-full bg-base-200 rounded-md p-1">
                <div class="flex-grow flex flex-col h-[calc(100%-48px)]">
                    <div class="py-2 pl-4 opacity-70 border border-base-content border-opacity-20 rounded-md flex flex-row justify-start bg-base-300 font-bold text-warning mb-2">
                        <span class="opacity-70">Active Chat:&nbsp;</span>
                        <span x-text="activeChatRoom.name"></span>
                    </div>
                    <div x-data
                        @activechatupdated.window="
                            if ($event.detail.scrollto == 'bottom') {
                            document.getElementById('chatsbottom').scrollIntoView();
                            } else {
                                document.getElementById('chatstop').scrollIntoView();
                            }
                        "
                        class="flex-grow rounded-md overflow-y-scroll border border-base-100">
                        <div x-show="activeChatRoom.noMoreMsgs != undefined && activeChatRoom.noMoreMsgs" class="p-1 text-center w-full bg-base-100">
                            <span>No more past messages.</span>
                        </div>
                        <div x-show="activeChatRoom.noMoreMsgs == undefined || activeChatRoom.noMoreMsgs == false" class="p-1 text-center w-full">
                            <button type="button" @click.prevent.stop="loadOlderMessages(activeChatRoom.id);" class="btn btn-xs btn-link normal-case opacity-60">Load more</button>
                        </div>
                        <div id="chatstop" class="h-4"></div>
                        <template x-for="c in activeChats">
                            <div class="flex flex-row w-full text-base-content opacity-80"
                                :class="c.sender_id == user.id ? 'justify-end' :'justify-start'">
                                <div class="flex flex-col max-w-3/4 border border-base-100 rounded-md bg-base-300 my-1">
                                    <div class="p-1 bg-base-100 text-xs">
                                        <span x-text="c.display_time.month + ' ' + c.display_time.date + ' ' + c.display_time.time"></span>
                                        :&nbsp;<span class="text-warning opacity-70" x-text="c.sender.name"></span>
                                    </div>
                                    <div class="p-1">
                                        <span x-text="c.message"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div id="chatsbottom" class="h-20"></div>
                    </div>
                </div>
                <div>
                    <form x-data="{
                            postMessage() {
                                let fd = new FormData($el);
                                axios.post(
                                    '{{route('internal_chat.post_message')}}',
                                    fd
                                ).then((r) => {
                                    $el.reset();
                                });
                            }
                        }"
                        @submit.prevent.stop="postMessage" action="" class="flex flex-row items-stretch my-1">
                        <input name="chat_room_id" type="hidden" :value="activeChatRoomId">
                        <input name="sender_id" type="hidden" :value="user.id">
                        <textarea name="text_message" class="flex-grow h-12 rounded-md bg-base-100 mr-1 text-base-content opacity-80"></textarea>
                        <button type="submit" class="btn btn-success normal-case h-full">Send</button>
                    </form>
                </div>
            </div>
            <div class="flex flex-col bg-base-200 p-1 rounded-md w-52 max-h-full">
                <div class="py-2 pl-4 opacity-70 border border-base-content border-opacity-20 rounded-md flex flex-row justify-between bg-base-300 font-bold text-warning mb-2">
                    <span>Team Mates</span>
                </div>
                <ul class="max-h-full overflow-y-scroll w-full">
                    <template x-for="df in dormantFriends">
                        <li class="py-2 pl-4 text-base-content opacity-70 border border-base-content border-opacity-20 rounded-md flex flex-row justify-between mb-2">
                            <span x-text="df.name"></span>
                            <button @click.prevent.stop="getChatRoom(df.id, 'App\\Models\\User')" type="button" class="btn btn-xs text-success">
                                <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-3" width="w-3"/>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
    <x-footer />
</x-easyadmin::app-layout>
