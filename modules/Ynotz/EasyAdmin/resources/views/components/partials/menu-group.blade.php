@props(['title' => 'Menu Item', 'icon' => 'easyadmin::icons.info'])
<a x-data="{collapsed: false}"
    @sidebarresize.window="collapsed = $event.detail.collapsed;" class="flex flex-row items-center my-0 text-sm px-2">
    <x-easyadmin::display.icon icon="{{$icon}}" height="h-4" width="w-4"/>
    <span class="inline-block py-2 transition-all" :class="collapsed ? 'w-0 px-0' : 'w-40 px-3'" x-transition>
        <span class="block w-36 transition-opacity" :class="!collapsed || 'opacity-0'">{{$title}}</span>
    </span>
</a>
