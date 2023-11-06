<x-easyadmin::app-layout>
    <div class="py-1 flex-grow flex flex-row h-full items-stretch w-full space-x-1">
        <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-md min-w-fit">
            <x-easyadmin::partials.sidebar />
        </div>
            <div x-data
            @contentupdate.window="
            console.log('content update in dashboard');
            if ($event.detail.target == 'renderedpanel') {
                $el.innerHTML = $event.detail.content;
            }
            "
            class="flex-grow bg-base-100 overflow-x-hidden shadow-sm sm:rounded-lg" id="renderedpanel">
                @fragment ('main-panel')
                    <div>
                        Welcome! This is your dashboard. Test
                    </div>
                @endfragment
            </div>
        <div class="h-screen w-screen fixed top-0 left-0 z-50 bg-base-200 opacity-30 flex flex-row justify-center items-center" x-show="ajaxLoading">
            <div>
                <img src="/images/loading.gif" class="h-24 w-24" alt="">
            </div>
        </div>
    </div>
</x-easyadmin::app-layout>
