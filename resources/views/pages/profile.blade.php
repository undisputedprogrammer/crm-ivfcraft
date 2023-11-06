<x-easyadmin::app-layout>


        <header class="bg-base-200 shadow">

            <div class="max-w-7xl text-base-content text-lg uppercase font-semibold mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $user->name }}
            </div>
        </header>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-base-100 shadow sm:rounded-lg">
                <div class="">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::app-layout>
