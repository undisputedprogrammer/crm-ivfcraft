@props(['appointments'])
<div class=" w-[96%] lg:w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($appointments != null && count($appointments)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Date</th>
              <th>Prospect name</th>
              <th>Prospect Contact No.</th>
              <th>Doctor</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($appointments as $appointment)
                <tr
                @click.prevent.stop="
                $dispatch('dataupdate',{
                    appointment: {{json_encode($appointment)}},
                    target: 'appointment-details'
                })"
                class="text-base-content hover:bg-base-100 relative">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$appointment->appointment_date}}</td>
                    <td>{{$appointment->lead->name}}</td>
                    <td>{{$appointment->lead->phone}}</td>
                    <td>{{$appointment->doctor->name ?? 'Not specified'}}</td>
                    <td class=" font-medium" x-text=" '{{$appointment->consulted_date}}' != '' ? 'Consulted' : 'Pending' " :class="'{{$appointment->consulted_date}}' != '' ?' text-success' : ' text-warning' "></td>

                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No appointments to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $appointments->links() }}
    </div>

</div>
