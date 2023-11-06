<div x-show="
    fp.remarks != undefined
    && fp.remarks != null
    && fp.remarks.length > 0
    && fp.next_followup_date == null
    && fp.lead.status != 'Closed'
    && fp.lead.status != 'Completed'
    && lead.status != 'Completed'
    " class="dropdown ">
    <label tabindex="0" class="btn btn-sm"
    @click.prevent.stop="dropdown.style.visibility = 'visible';"><span x-text="selected_action"></span><x-icons.down-arrow /></label>
    <ul id="followup-action-dropdown" tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52"
        :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral'">
        <li>
            <a @click.prevent.stop=" selected_action = '-- Select Action --';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == '' ? ' text-primary hover:text-primary' : ''"> -- Select Action --
            </a>
        </li>

        <li x-show="lead.appointment != null && lead.status == 'Appointment Fixed'">
            <a @click.prevent.stop=" selected_action = 'Consulted';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Consulted' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Mark as consulted
            </a>
        </li>
        <li>
            <a @click.prevent.stop=" selected_action = 'Add Followup';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Add Followup' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Add Followup
            </a>
        </li>
        <li x-show="lead.status == 'Created' || lead.status == 'Follow-up Started'">
            <a @click.prevent.stop="selected_action = 'Schedule Appointment';
            dropdown.style.visibility = 'hidden';"
                class=" " :class="selected_action == 'Schedule Appointment' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Schedule Appointment
            </a>
        </li>
        <li x-show="lead.status != 'Closed' && lead.status != 'Consulted'">
            <a @click.prevent.stop=" selected_action = 'Close Lead';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Close Lead' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Close Lead
            </a>
        </li>
        <li x-show="lead.status == 'Consulted'">
            <a @click.prevent.stop=" selected_action = 'Complete';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Complete' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Mark as complete
            </a>
        </li>

        <li x-show="lead.appointment != null && lead.status == 'Appointment Fixed'">
            <a @click.prevent.stop=" selected_action = 'Reschedule Appointment';
            dropdown.style.visibility = 'hidden'; "
                class="" :class="selected_action == 'Reschedule Appointment' ? ' text-primary hover:text-primary' : 'hover:text-neutral-content'">Reschedule Appointment
            </a>
        </li>

    </ul>
</div>

<div x-show="lead.status == 'Closed'" class=" font-medium text-error">
    Lead is Closed, No further actions can be performed.
</div>


