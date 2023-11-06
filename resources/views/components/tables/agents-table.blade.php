@props(['agents'])
<div class=" w-[96%] lg:w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($agents != null && count($agents)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($agents as $agent)
                <tr class="text-base-content hover:bg-base-100 relative cursor-pointer">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$agent->id}}</td>
                    <td>{{$agent->name}}</td>
                    <td>{{$agent->email}}</td>
                    <td class=" flex">
                        <button @click.prevent.stop="$dispatch('agentedit', {id: {{$agent->id}}, name: '{{$agent->name}}', email: '{{$agent->email}}'});" class="btn btn-ghost btn-xs text-warning" type="button">
                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                        </button>
                        <button class="btn btn-ghost btn-xs text-warning"
                        @click.prevent.stop="$dispatch('agentattendance', {id: {{$agent->id}},name: '{{$agent->name}}'});">
                            <x-icons.attendance-icon/>
                        </button>
                        <button class="btn btn-ghost btn-xs text-warning"
                        @click.prevent.stop="$dispatch('agentjournals', {id: {{$agent->id}},name: '{{$agent->name}}'});">
                            <x-icons.journal-icon/>
                        </button>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No agents to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $agents->links() }}
    </div>

</div>
