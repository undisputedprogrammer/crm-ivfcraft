@props(['doctors'])
<div class=" w-[96%] md:w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($doctors != null && count($doctors)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Name</th>
              <th>Department</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($doctors as $doctor)
                <tr class="text-base-content hover:bg-base-100 relative">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$doctor->name}}</td>
                    <td>{{$doctor->department ?? 'Not specified'}}</td>
                    <td>
                        <button @click.prevent.stop="$dispatch('doctoredit', {id: {{$doctor->id}}, name: '{{$doctor->name}}', department: '{{$doctor->department}}', center_id: '{{$doctor->center_id}}'});" class="btn btn-ghost btn-xs text-warning" type="button">
                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                        </button>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No doctors to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $doctors->links() }}
    </div>

</div>
