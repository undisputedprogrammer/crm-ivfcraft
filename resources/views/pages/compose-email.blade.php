<x-easyadmin::app-layout>

    <div x-data="x_emails" class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black "
    x-init="lead = {{$lead}};">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <div class=" flex flex-col justify-evenly items-start w-full bg-base-200 pt-1.5 pl-[3.3%] space-x-2">
        <h1 class=" text-primary text-xl font-semibold bg-base-200 ">Compose Email</h1>

        <div class=" flex w-full mt-8 justify-center space-x-10">



            <div class=" bg-base-100 rounded-lg p-6 min-w-[65%] flex flex-col items-center">
                <div class="flex space-x-2 justify-center">
                    <p class=" text-primary font-medium text-center text-base">Sending Email to {{$lead->email}}</p>

                    {{-- tooltip --}}
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-circle btn-ghost btn-xs text-info">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </label>
                        <div tabindex="0" class="card compact dropdown-content z-[1] shadow bg-neutral rounded-box w-64">
                          <div class="card-body">
                            <h1 class="text-secondary font-medium">Available Placeholders</h1>
                            <div class="flex flex-col mt-3">
                                <p class=" font-medium text-base-content">
                                    <span class="">Lead Name : </span>
                                    <span class="text-warning">{name}</span>
                                </p>
                                <p class=" font-medium text-base-content">
                                    <span class="">Lead Phone : </span>
                                    <span class="text-warning">{phone}</span>
                                </p>
                                <p class=" font-medium text-base-content">
                                    <span class="">Appointment Date : </span>
                                    <span class="text-warning">{appointment}</span>
                                </p>
                                <p class=" font-medium text-base-content">
                                    <span class="">Appointed Doctor : </span>
                                    <span class="text-warning">{doctor}</span>
                                </p>
                            </div>
                          </div>
                        </div>
                    </div>
                    {{-- tooltip ends --}}

                </div>

                <form id="custom-email-form" action=""
                @submit.prevent.stop="sendCustomMail($el,'{{route('email.send')}}');"
                class=" w-full flex flex-col items-center space-y-2 mt-8"
                @formresponse.window="
                if($el.id == $event.detail.target){
                    postFormResponse($event.detail.content,$el);
                }">
                    <input type="text" required name="subject" placeholder="Email Subject" class="input input-bordered focus:outline-none input-secondary min-w-72 lg:w-[50%] text-base-content focus:text-base-content">

                    <textarea name="body" required autocomplete="off" placeholder="Enter Email Body" class=" textarea textarea-bordered textarea-secondary focus:outline-none min-w-72 lg:w-[50%] text-base-content focus:text-base-content"></textarea>

                    <button type="submit" class=" btn btn-secondary btn-sm  text-base normal-case">Send</button>
                </form>

            </div>

        </div>

      </div>
    </div>
</x-easyadmin::app-layout>
