<x-easyadmin::app-layout>
<div class=" " >

    <x-sections.side-drawer/>
    <div class=" bg-base-200 h-[calc(100vh-3.5rem)] flex flex-col justify-center ">
        <h1 class=" text-center text-primary font-semibold text-lg mb-2">Change Password</h1>
        <form
        x-data ="
        { doSubmit() {
            let form = document.getElementById('password-reset-form');
            let formdata = new FormData(form);
            $dispatch('formsubmit',{url:'{{route('password.change')}}', route: 'password.change',fragment: 'page-content', formData: formdata, target: 'password-reset-form'});
        }}"
        @submit.prevent.stop="doSubmit();"

        @formresponse.window="
        if($el.id == $event.detail.target){
            console.log($event.detail);
            if ($event.detail.content.success) {
                $el.reset();
                $dispatch('showtoast', {mode: 'success', message: $event.detail.content.message});

                $dispatch('linkaction', {link: '{{route('overview')}}', route: 'overview'});

            } else {
                $dispatch('showtoast', {mode: 'error', message: $event.detail.content.message});
            }


        }
        "
        id="password-reset-form"
        method="" action=""
        class=" min-w-100 max-w-md mx-auto bg-base-100 p-4 rounded-lg text-base-content flex flex-col space-y-3">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="old-password" class=" font-medium">Old Password</label>
                <input type="password" id="old-password" name="current_password" class=" input w-full input-primary mt-1 focus:ring-0">
            </div>

            <div>
                <label for="new-password" class=" font-medium">New Password</label>
                <input type="password" id="new-password" name="password" class=" input w-full input-primary mt-1 focus:ring-0">
            </div>

            <div>
                <label for="confirm-password" class="font-medium">Confirm Password</label>
                <input type="password" id="confirmed-password" name="password_confirmation" class=" input w-full input-primary mt-1 focus:ring-0">
            </div>


            <div class="flex items-center justify-end mt-4">
                <button type="submit" class=" btn btn-sm btn-primary">Change Password</button>
            </div>
        </form>

    </div>
</div>
</x-easyadmin::app-layout>
