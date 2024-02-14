<x-easyadmin::app-layout>

    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black "
        @pageaction.window="
            page = $event.detail.page;
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
            })">


        <x-sections.side-drawer />
        {{-- page body --}}
        <div x-data="{
            currentForm: 'create',
            campaigns: [],
            selectedCampaign: null,
            toggleCampaign(cid) {
                $dispatch('formsubmit', {url: '{{route('campaign.toggle', ['id' => '_X_'])}}'.replace('_X_', cid), target: 'campaign_toggle_form'});
            },
            toggleCampaignForm(cid) {
                $dispatch('formsubmit', {url: '{{route('campaign.toggle_form', ['id' => '_X_'])}}'.replace('_X_', cid), target: 'campaign_toggle_form'});
            },
            {{-- toggleEdit(cid) {
                this.selectedCampaign = this.campaigns.filter((c) => {
                    return c.id == cid;
                })[0];
                this.currentForm = 'edit';
            }, --}}
            resetEdit() {
                this.currentForm = 'create';
                this.selectedCampaign = null;
            },
            returnRefresh() {
                let urlParams = new URLSearchParams(window.location.search);
                console.log(urlParams.get('page'));
                if (urlParams.has('page')) {
                    console.log('has params')
                    this.$dispatch('linkaction', { link: '{{ route('campaign.index') }}', route: 'campaign.index', fragment: 'page-content', fresh: true, params: { page: urlParams.get('page') } });
                } else {
                    $dispatch('linkaction', { link: '{{ route('campaign.index') }}', route: 'campaign.index', fragment: 'page-content', fresh: true });
                }
            }
        }" x-init="
            temp = {{ Js::from($campaigns) }};
            campaigns= temp.data;
        ;
        "
            class=" flex flex-col justify-evenly items-start w-full bg-base-200 pt-7 pl-[3.3%] space-x-2">

            <h1 class=" font-bold text-primary text-lg w-full">Campaigns</h1>

            <div class=" flex flex-row lg:flex-row items-start w-full  space-y-3 lg:space-y-0 lg:space-x-8 mt-4">

                <div class=" flex flex-col lg:w-2/5">
                    <div class=" rounded-lg  border-secondary border overflow-x-auto w-full">
                        <table class="table table-sm  rounded-lg">
                            <thead class="rounded-t-lg">
                                <tr class=" text-secondary  sticky top-0 bg-base-300">
                                    {{-- <th>Code</th> --}}
                                    <th>Name</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Created at</th>
                                    <th>Enabled?</th>
                                    {{-- <th>Show in form dropdowns?</th> --}}
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($campaigns as $campaign)
                                    <tr class="text-base-content hover:bg-base-100 cursor-pointer">


                                        {{-- <th>{{ $campaign->code }}</th> --}}
                                        <td>{{ $campaign->name }}</td>
                                        {{-- <td
                                        x-text="{{$campaign->is_enabled}} ? 'Enabled' : 'Disabled'"
                                        class=" font-medium"
                                        :class="{{$campaign->is_enabled}} ? ' text-success' : ' text-error'">
                                    </td> --}}
                                        <td>{{ $campaign->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="form-control">
                                            <label class="label cursor-pointer justify-center">
                                                {{-- <span class="label-text">Remember me</span> --}}
                                                <input @change="toggleCampaign({{$campaign->id}})" type="checkbox" @if($campaign->isEnabled) checked @endif class="checkbox checkbox-primary" />
                                            </label>
                                            </div>
                                        </td>
                                        {{-- <td>
                                            <div class="form-control">
                                            <label class="label cursor-pointer justify-center">
                                                <input @change="toggleCampaignForm({{$campaign->id}})" type="checkbox" @if($campaign->enable_in_forms) checked @endif class="checkbox checkbox-primary" />
                                            </label>
                                            </div>
                                        </td> --}}

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class=" mt-1.5">
                        {{ $campaigns->links() }}
                    </div>

                </div>


                {{-- ---------FORM --}}

                <div class="p-6 rounded-lg bg-base-100 md:w-2/5">

                    {{-- *************Source create form************** --}}
                    <h1 x-show="currentForm == 'create'" class=" font-medium text-base text-primary">Create new campaign</h1>

                    <p class=" text-error text-sm" id="error-displayer"></p>

                    <form x-show="currentForm == 'create'" x-data="{
                        doSubmit() {
                            let form = document.getElementById('campaign-create-form');
                            let formdata = new FormData(form);
                            $dispatch('formsubmit', { url: '{{ route('campaign.store') }}', route: 'campaign.store', fragment: 'page-content', formData: formdata, target: 'campaign-create-form' });
                        }
                    }"
                        @formresponse.window="
                        if ($event.detail.target == 'campaign_toggle_form') {
                            if ($event.detail) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: $event.detail.content.mode});
                            } else {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: $event.detail.content.mode});
                            }
                        }
                        if ($event.detail.target == 'campaign_toggle') {
                            if ($event.detail) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: $event.detail.content.mode});
                            } else {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: $event.detail.content.mode});
                            }
                        }
                        console.log($event.detail);
                        console.log($el.id);
                        if($event.detail.target == $el.id){
                            console.log($event.detail.content);
                            if ($event.detail.content.success) {
                                console.log('campaigns');
                                console.log(campaigns);
                                campaigns.push($event.detail.content.campaign);
                                console.log(campaigns);
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                returnRefresh();
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }" @submit.prevent.stop="doSubmit();" id="campaign-create-form" class=" flex flex-col space-y-3 mt-2.5">
                        <input type="text" name="name" required class="input input-bordered text-base-content focus:outline-none min-w-72" placeholder="Enter campaign name">

                        <button type="submit" class=" btn btn-primary w-fit btn-sm">Save</button>

                    </form>


                    {{-- ***************Source edit form*************** --}}


                </div>
            </div>




        </div>




    </div>
    </div>

    </div>

    </div>

    </div>
</x-easyadmin::app-layout>
