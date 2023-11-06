<dialog id="edit_question_modal" class="modal z-40 ">
    <div class=" modal-box " @click.outside="document.getElementById('edit-modal-close-btn').click();">
    <form
    x-data = "{ doSubmit() {
        let form = document.getElementById('edit-question-form');
        let formdata = new FormData(form);
        formdata.append('id',edit_question_id);
        $dispatch('formsubmit',{url:'{{route('update-question')}}', route: 'update-question',fragment: 'page-content', formData: formdata, target: 'edit-question-form'});
    }}"
    id="edit-question-form"
    @submit.prevent.stop="doSubmit();"
    @formresponse.window="
        if ($event.detail.target == $el.id) {
            if ($event.detail.content.success) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                {{-- questions = $event.detail.content.questions; --}}
                $dispatch('questionupdate',{target: $event.detail.content.question.question_code, question: $event.detail.content.question.question});
                document.getElementById('edit-modal-close-btn').click();
                $dispatch('formerrors', {errors: []});
                } else if (typeof $event.detail.content.errors != undefined) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                    } else{
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                        }
            $el.reset();
        }"
     class=" bg-base-100 text-base-content flex flex-col"
     method=""
     action="">
      <h3 class="font-bold text-lg text-secondary">Edit Question</h3>
      <input type="text" placeholder="" name="question" :value="edit_question" class="input input-sm w-full bg-base-200 max-w-xs md:max-w-md" />
      <button class="btn btn-sm btn-success mt-2 w-fit" type="submit">Save</button>
    </form>
      <div class="flex space-x-2 mt-2.5">


        <form action="" method="dialog" class="hidden">
            <button id="edit-modal-close-btn" class="btn btn-error btn-sm">Close</button>
        </form>

      </div>


    </div>
</dialog>
