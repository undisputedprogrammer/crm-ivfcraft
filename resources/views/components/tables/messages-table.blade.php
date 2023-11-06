@props(['messages'])
<div class="w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($messages != null && count($messages)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Template</th>
              <th>Message</th>
              {{-- <th></th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($messages as $message)
                <tr class="text-base-content hover:bg-base-100 relative">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$message->template}}</td>
                    <td>{{$message->payload ?? 'Not defined'}}</td>
                    {{-- <td>
                        <button @click.prevent.stop="

                        $dispatch('editmessage', {id: {{$message->id}}, template: '{{$message->template}}', message: '{{$message->message}}'});"
                        class="btn btn-ghost btn-xs text-warning" type="button">
                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                        </button>
                    </td> --}}
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No messages to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $messages->links() }}
    </div>

</div>
