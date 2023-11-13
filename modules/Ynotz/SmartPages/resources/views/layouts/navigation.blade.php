<nav x-data="{ open: false }" class="bg-base-100 border-b border-base-200">
    <!-- Primary Navigation Menu -->
    <div x-data="{navcollapsed: false}" @navresize.window="navcollapsed = $event.detail.navcollapsed;" x-show="!navcollapsed" class="px-2 sm:px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center mr-2 sm:hidden">
                    <x-easyadmin::display.icon x-data="{sidebarhidden: true}"
                        icon="icons.menu" height="h-7" width="h-7"
                        @click="sidebarhidden=false; $dispatch('sidebarvisibility', {'hidden': false});"/>
                </div>
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- <x-application-logo class="block h-10 w-auto fill-current text-base-content" /> --}}
                        <img src="{{asset('images/logo.png')}}" alt="logo" class="h-8">
                    </a>
                </div>

                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}
            </div>
            <div class="hidden sm:flex flex-row space-x-6">
                <x-easyadmin::utils.theme-switch/>
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-accent-content hover:text-accent-content hover:border-base-200 focus:outline-none focus:text-accent-content focus:border-base-200 transition duration-150 ease-in-out">
                                <div class="flex flex-row space-x-2 items-center">{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <x-easyadmin::display.icon icon="icons.user"/>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            {{-- <button class="block w-full px-4 py-2 text-sm leading-5 text-base-content hover:bg-base-200 focus:outline-none focus:bg-base-200 transition duration-150 ease-in-out text-left"
                                    @click.prevent.stop="$dispatch('passwordform');">
                                {{ __('Change Password') }}
                            </button> --}}
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" class="p-0 m-0">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex space-x-2 items-center sm:hidden">
                <x-easyadmin::utils.theme-switch/>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-base-content hover:text-base-content hover:bg-base-100 focus:outline-none focus:bg-base-100 focus:text-base-content transition duration-150 ease-in-out">
                    <x-easyadmin::display.icon icon="icons.user" height="h-6" width="w-6"/>
                    {{-- <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg> --}}
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-base-content">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-base-content">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
