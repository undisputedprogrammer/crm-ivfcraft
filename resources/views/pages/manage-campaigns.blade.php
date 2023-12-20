<x-easyadmin::app-layout>

    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black "

    @pageaction.window="
            page = $event.detail.page;
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
            })"
    >


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
        },
        returnRefresh(){
            let urlParams = new URLSearchParams(window.location.search);
            console.log(urlParams.get('page'));
            if(urlParams.has('page')){
                console.log('has params')
                this.$dispatch('linkaction',{link: '{{route('sources.index')}}', route: 'sources.index', fragment: 'page-content', fresh: true, params: {page: urlParams.get('page')} });
            }
            else{
                $dispatch('linkaction',{link: '{{route('sources.index')}}', route: 'sources.index', fragment: 'page-content', fresh: true});
            }
        }
      }"

       class=" flex flex-col justify-evenly items-start w-full bg-base-200 pt-7 pl-[3.3%] space-x-2">

        <h1 class=" font-bold text-primary text-lg w-full">Campaigns</h1>

        <div class=" flex flex-col lg:flex-row items-start w-full  space-y-3 lg:space-y-0 lg:space-x-8 mt-4">

            <div class=" flex flex-col lg:w-[40%]">
                <div class=" rounded-lg  border-secondary border overflow-x-auto w-full">
                    <table class="table table-sm  rounded-lg">
                        <thead class="rounded-t-lg">
                            <tr class=" text-secondary  sticky top-0 bg-base-300">
                                {{-- <th>Code</th> --}}
                                <th>Name</th>
                                {{-- <th>Status</th> --}}
                                <th>Created at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($campaigns as $campaign)
                                <tr class="text-base-content hover:bg-base-100 cursor-pointer">


                                    {{-- <th>{{ $campaign->code }}</th> --}}
                                    <td>{{$campaign->name}}</td>
                                    {{-- <td
                                        x-text="{{$source->is_enabled}} ? 'Enabled' : 'Disabled'"
                                        class=" font-medium"
                                        :class="{{$source->is_enabled}} ? ' text-success' : ' text-error'">
                                    </td> --}}
                                    <td>{{ $campaign->created_at->format('d M Y') }}</td>
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

                <div class=" mt-1.5">
                    {{$campaigns->links()}}
                </div>

            </div>

        </div>

      </div>

    </div>
</x-easyadmin::app-layout>
