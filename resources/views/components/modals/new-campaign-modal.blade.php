@props(['formId'])
<div x-show="showCampaignForm"
    class="fixed top-0 left-0 z-20 flex flex-row justify-center items-center bg-base-200 bg-opacity-80 w-screen h-screen ">
    <div class="p-6 rounded-lg bg-base-100 md:w-2/5">

        {{-- *************Source create form************** --}}
        <h1 class=" font-medium text-base text-primary">Create new campaignX</h1>

        <p class=" text-error text-sm" id="error-displayer"></p>

        <div x-data="{
                campaign: '',
            }
            " id="{{$formId}}" class=" flex flex-col space-y-3 mt-2.5">
            <input x-model="campaign" type="text" name="name" required
                class="input input-bordered text-base-content focus:outline-none min-w-72"
                placeholder="Enter campaign name">
            <div class="flex flex-row justify-between">
                <button @click.prevent.stop="showCampaignForm = false;" type="button"
                    class=" btn btn-ghost w-fit btn-sm text-base-content">Cancel</button>
                <button @click.prevent.stop="createCampaign(campaign);" type="button"
                    class=" btn btn-primary w-fit btn-sm">Save</button>
            </div>

        </div>

    </div>
</div>
