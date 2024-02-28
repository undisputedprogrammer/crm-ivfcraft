{{-- question visit within a week --}}
<div x-data="{
    visit_dropdown : document.getElementById('visit-question-dropdown')
}" class="flex items-center space-x-2">
    <p class=" text-sm font-medium">Visit within a month?: </p>
    <div class="dropdown">
        <label tabindex="0" class="btn btn-sm"
        @click.prevent.stop="visit_dropdown.style.visibility ='visible' "><span x-text="lead.q_visit == null || lead.q_visit == 'null' ? 'Not selected' : lead.q_visit " class=" text-secondary"></span><x-icons.down-arrow /></label>

        <ul id="visit-question-dropdown" tabindex="0" class="dropdown-content z-[20] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_visit,
                    q_answer : 'null',
                    question : 'q_visit'
                });
                visit_dropdown.style.visibility ='hidden';" class=" " :class="lead.q_visit == null ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_visit,
                    q_answer : 'yes',
                    question : 'q_visit'
                });
                visit_dropdown.style.visibility ='hidden';" class=" " :class="lead.q_visit == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_visit,
                    q_answer : 'no',
                    question : 'q_visit'
                });
                visit_dropdown.style.visibility ='hidden';" class="" :class="lead.q_visit == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
        </ul>

      </div>
</div>

{{-- question decide within a week --}}
<div x-data="{
    decide_dropdown : document.getElementById('decide-question-dropdown')
}" x-show="lead.q_visit == 'no'" x-cloak class="flex items-center space-x-2 mt-1">
    <p class=" text-sm font-medium">Decide within a week ? : </p>
    <div class="dropdown">
        <label tabindex="0" class="btn btn-sm"
        @click.prevent.stop="decide_dropdown.style.visibility ='visible';" ><span x-text="lead.q_decide == null || lead.q_decide == '' ? 'Not selected' : lead.q_decide " class=" text-secondary"></span><x-icons.down-arrow /></label>

        <ul id="decide-question-dropdown" tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_decide,
                    q_answer : 'null',
                    question : 'q_decide'
                });
                decide_dropdown.style.visibility = 'hidden';" class=" " :class="lead.q_decide == null || lead.q_decide == '' ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_decide,
                    q_answer : 'yes',
                    question : 'q_decide'
                });
                decide_dropdown.style.visibility = 'hidden';" class=" " :class="lead.q_decide == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
            <li><a @click.prevent.stop="
                $dispatch('changequestion',{
                    link: '{{route('lead.answer')}}',
                    current: lead.q_decide,
                    q_answer : 'no',
                    question : 'q_decide'
                });
                decide_dropdown.style.visibility = 'hidden';" class="" :class="lead.q_decide == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
        </ul>

      </div>
</div>
