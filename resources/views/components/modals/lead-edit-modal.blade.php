<div x-show="editLead" x-cloak x-transition class=" absolute w-full h-screen z-30 bg-neutral bg-opacity-70">

    <div class="md:w-[40%] h-fit rounded-lg bg-base-100 mx-auto mt-14 bg-opacity-100 flex flex-col items-center p-4">
        <h1 class="text-secondary font-medium text-lg">Edit lead details</h1>

        <form @submit.prevent.stop="updateLead($el, lead.id, '{{route('lead.update')}}');"
        id="lead-edit-form" action="" class=" flex flex-col space-y-2 mt-4 w-full items-center text-base-content"
        @formresponse.window="
        if($event.detail.target == $el.id){
            editLead = false;
            if ($event.detail.content.success) {
                lead.name = $event.detail.content.lead.name;
                lead.city = $event.detail.content.lead.city;
                lead.email = $event.detail.content.lead.email;
                if(leads != null && leads != undefined){
                    leads[lead.id] = lead;
                }
                {{-- to change the lead name and city in the table row --}}
                document.getElementById('name-'+lead.id).innerText = lead.name;
                document.getElementById('city-'+lead.id).innerText = lead.city;
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
            } else if (typeof $event.detail.content.errors != undefined) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

            } else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }
        }">

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Name :</label>
                <input :readonly="lead.name != 'unknown lead'" :value="lead.name" required type="text" name="name" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">City :</label>
                <input :readonly="lead.city != 'Not specified'" :value="lead.city" required type="text" name="city" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex-col flex">
                <label for="" class="font-medium text-base-content">Email :</label>
                <input :readonly="lead.email != 'Not specified'" :value="lead.email" required :type="lead.email == 'Not specified' ?  'text' : 'email' " name="email" class=" input input-bordered input-secondary md:w-96 focus:outline-none min-w-72">
            </div>

            <div class=" flex space-x-2 md:w-96">
                <button type="submit" class=" btn btn-success btn-sm">Save</button>
                <button @click.prevent.stop="editLead = false;" class=" btn btn-error btn-sm">Cancel</button>
            </div>
        </form>
    </div>

</div>
