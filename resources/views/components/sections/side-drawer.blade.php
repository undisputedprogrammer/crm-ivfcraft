<div x-show="sidedrawer" @click.outside="sidedrawer = !sidedrawer"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-52 "
        x-transition:enter-end="translate-x-0 "
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 "
        x-transition:leave-end="-translate-x-52 "
         class=" absolute  w-52 md:hidden h-full top-[3.5rem] z-50 bg-base-100 opacity-100">
    <ul class="px-4 py-3">

        <li @click.prevent.stop="$dispatch('linkaction',{
            link:'{{route('overview')}}',
            route: 'overview',
            fragment: 'page-content',
            fresh : true
        })" class=" py-1.5 my-1  px-1.5 rounded-lg text-base-content font-medium"
        :class="currentroute =='overview' ? ' bg-base-200 opacity-100' : 'opacity-60 hover:opacity-100' ">Dashboard</li>

        <li @click.prevent.stop="$dispatch('linkaction',{
            link:'{{route('fresh-leads')}}',
            route: 'fresh-leads',
            fragment: 'page-content',
            fresh : true

        })" class=" py-1.5 my-1 px-1.5 rounded-lg text-base-content font-medium" :class="currentroute =='fresh-leads' ? ' bg-base-200 opacity-100' : 'opacity-60 hover:opacity-100' ">Fresh Leads</li>

        <li @click.prevent.stop="$dispatch('linkaction',{
            link:'{{route('followups')}}',
            route: 'followups',
            fragment: 'page-content',
            fresh : true

        })" class=" py-1.5 my-1  px-1.5 rounded-lg text-base-content font-medium"
        :class="currentroute =='followups' ? ' bg-base-200 opacity-100' : 'opacity-60 hover:opacity-100' ">Followups</li>

        <li @click.prevent.stop="$dispatch('linkaction',{
            link:'{{route('search-index')}}',
            route: 'search-index',
            fragment: 'page-content'
        })" class=" py-1.5 my-1  px-1.5 rounded-lg text-base-content font-medium"
        :class="currentroute =='search-index' ? ' bg-base-200 opacity-100' : 'opacity-60 hover:opacity-100' ">Search</li>
    </ul>
</div>


