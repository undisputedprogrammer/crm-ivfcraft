<section>
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Profile Information') }}
        </h2>
    </header>

    <form x-data="{
            hasPicture: false,
            editMode: false,
            setEditMode(mode = true) {
                this.editMode = mode;
            },
            doSubmit(){
                let form = document.getElementById('profile-update-form');
                let formdata = new FormData(form);
                $dispatch('formsubmit',{url:'{{route('profile.save')}}', route: 'profile.save',fragment: 'page-content', formData: formdata, target: 'profile-update-form'});
            }
        }"
        @submit.prevent.stop="doSubmit();"
        @formresponse.window="
            if ($event.detail.target == $el.id) {
                if ($event.detail.content.success) {
                        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                        $dispatch('linkaction',{link: '{{route('user.profile')}}', route: 'user.profile', fragment: 'page-content', fresh: true});
                        $dispatch('formerrors', {errors: []});
                    } else if (typeof $event.detail.content.errors != undefined) {
                        $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                    } else{
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                    }
            }
        "
        x-init="
            hasPicture = {{ auth()->user()->user_picture == null ? 'false' : 'true' }};
        "
        id="profile-update-form" class="max-w-72 m-auto mt-6 space-y-6 flex flex-col w-full justify-evenly items-center">
        @csrf

        <div x-show="!hasPicture || editMode" class="border border-base-content border-opacity-20 rounded-lg p-4 text-center">
            @php
                $element = [
                'key' => 'user_picture',
                'label' => 'Profile Picture',
                'authorised' => true,
                'validations' => [
                    'max_size' => '200 kb',
                    'mime_types' => ['image/jpg', 'image/jpeg', 'image/png']
                    ]
                ];
            @endphp
            <x-easyadmin::inputs.imageuploader :element="$element"/>
            <button x-show="hasPicture" @click.prevent.stop="setEditMode(false);" type="button" class="btn btn-link font-normal">
                Cancel
            </button>
        </div>
        <div x-show="hasPicture && !editMode" class="relative border border-base-content border-opacity-50 p-2 rounded-lg">
            <img src="{{auth()->user()->user_picture != null ? auth()->user()->user_picture['path'] : ''}}" class="h-52 rounded-lg m-auto">
            <button type="button" @click.prevent.stop="setEditMode();" class="btn btn-warning btn-sm absolute top-2 right-2">
                <x-easyadmin::display.icon icon="easyadmin::icons.edit"
                    height="h-4" width="w-4"/>
            </button>
        </div>

        <div class=" flex flex-col items-center space-y-2">

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block min-w-72 text-black" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>


        <div class="flex items-center justify-center p-8 text-center w-full">
            <button type="submit" class="btn btn-success btn-sm px-4">{{ __('Save') }}</button>
        </div>

    </div>


    </form>
</section>
