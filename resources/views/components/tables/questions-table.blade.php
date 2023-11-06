@props(['questions'])

<div x-data="{
    questions: {{ json_encode($questions->items()) }},
    edit_question: '',
    edit_question_id: null
}"
    @pageaction.window="
            console.log($event.detail);
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
    })"
    class="w-fit mx-auto">

    <x-forms.add-question-form />

    <x-modals.edit-question-modal />

    <div class="overflow-auto w-[96%] xl:w-fit mx-auto border border-primary rounded-xl mt-3">

        <table class="table overflow-auto mx-auto  ">

            <thead class="">
                <tr class=" text-secondary overflow-auto text-sm">
                    <th></th>
                    <th>Code</th>
                    <th>Question</th>
                    <th class="hidden ">Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>


                <template x-for="question in questions">
                    <tr :id="question.question_code" class=" text-base-content hover:bg-base-100 overflow-auto"
                        @questionupdate.window="
        if($el.id == $event.detail.target){
            question.question = $event.detail.question;
        }">
                        <th x-text="question.id"></th>
                        <td x-text="question.question_code" class="text-center"></td>
                        <td x-text="question.question"></td>
                        <td x-text="question.created_at" class=" hidden"></td>
                        <td class="flex ">
                            <button class="w-6 h-6 p-1 hover:bg-base-200 rounded-md">
                            <svg @click.prevent.stop="
            edit_question=question.question;
            edit_question_id=question.id;
            edit_question_modal.showModal();
            "
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="  stroke-primary ">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>




                        </td>
                    </tr>
                </template>


            </tbody>
        </table>


    </div>
    <div class="mt-1.5">
        {{ $questions->links() }}
    </div>

</div>
