<div x-show="lead.status == 'Created'" class="dropdown mt-1.5">
    <span></span>
    <label tabindex="0" class="btn btn-sm" @click="dropdown.style.visibility = 'visible';"><span x-text="selected_action"></span><x-icons.down-arrow /></label>
    <ul tabindex="0" id="lead-action-dropdown" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52 text-neutral-content"
        :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
        <li>
            <a @click.prevent.stop="selected_action = 'Initiate Followup';
            dropdown.style.visibility = 'hidden'; "
                class=" " :class="selected_action == 'Initiate Followup' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Initiate Followup
            </a>
        </li>
        <li>
            <a @click.prevent.stop="selected_action = 'Schedule Appointment';
            dropdown.style.visibility = 'hidden';"
                class=" " :class="selected_action == 'Schedule Appointment' ? ' text-primary hover:text-primary' : ' hover:text-neutral-content'">Schedule Appointment
            </a>
        </li>
        <li>
            <a @click.prevent.stop=" selected_action = 'Close Lead';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Close Lead' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Close Lead
            </a>
        </li>
    </ul>
</div>
