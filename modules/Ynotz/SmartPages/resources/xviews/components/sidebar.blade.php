<div x-data="{hidden: false}"
    x-init="hidden = screen.width < 768;"
    @sidebarvisibility.window="hidden=$event.detail.hidden;"
    class="overflow-hidden fixed top-0 left-0 z-50 sm:relative bg-base-100 w-full sm:w-auto min-w-fit ransition-all"
    :class="!hidden || 'w-0'">
    <div class="border-b border-base-300">
        <span x-data="{sidebarcollapse: false, collapsed: false}"
            class="w-full sm:w-auto flex flex-row items-center text-sm px-2 bg-base-200">
            <x-easyadmin::display.icon icon="icons.go_left" height="h-4" width="w-4"
            @click="sidebarcollapse=!sidebarcollapse; collapsed=sidebarcollapse; $dispatch('sidebarresize', {'collapsed': sidebarcollapse});" class="hidden sm:inline-block transition-all"  x-bind:class="!collapsed || 'rotate-180'"/>
            <x-easyadmin::display.icon icon="icons.close" height="h-4" width="w-4"
            @click="$dispatch('sidebarvisibility', {'hidden': true});" class="sm:hidden"/>
            <span class="block py-2 overflow-hidden transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-3'" x-transition>
                <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">&nbsp;</span>
            </span>
        </span>
    </div>
    <ul>
        @foreach ($sidebar_data as $item)
            @if ($item['type'] == 'menu_group')
                <div x-data="{group_expand: false}">
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <li class="flex flex-row items-center justify-start font-bold">
                    <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/>
                    <span class="transition-all" :class="group_expand ? 'rotate-180' : 'rotate-0'" @click.prevent.stop="group_expand = !group_expand;">
                        <x-easyadmin::display.icon icon="icons.down" height="h-4" width="w-4" class="mx-2 z-20"/>
                    </span>
                </li>
                <ul x-show="group_expand" x-transition>
                    @foreach ($item['menu_items'] as $mi)
                        @if ($mi['type'] == 'menu_item' && (!isset($mi['show']) || (isset($mi['show']) && $mi['show'])))
                        <li><x-easyadmin::partials.menu-item title="{{$mi['title']}}" route="{{$mi['route']}}" href="{{route($mi['route'], $mi['route_params'])}}" icon="{{$mi['icon']}}"/></li>
                        @endif
                    @endforeach
                </ul>
                @endif
                </div>
            @elseif ($item['type'] == 'menu_item')
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <li><x-easyadmin::partials.menu-item title="{{$item['title']}}" route="{{$item['route']}}" href="{{route($item['route'], $item['route_params'])}}" icon="{{$item['icon']}}"/></li>
                @endif
            @elseif ($item['type'] == 'menu_section')
            @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
            <li class="flex flex-row items-center justify-start bg-base-200 mt-4">
                <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/><span>:</span>
            </li>
            @endif
            @endif
        @endforeach
    </ul>
</div>
