<form
x-data = "{ doSubmit() {
    let form = document.getElementById('add-question-form');
    let formdata = new FormData(form);
    $dispatch('formsubmit',{url:'{{route('add-question')}}', route: 'add-question',fragment: 'page-content', formData: formdata, target: 'add-question-form'});
}}"
@submit.prevent.stop="doSubmit();"
@formresponse.window="
        if ($event.detail.target == $el.id) {
            if ($event.detail.content.success) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});

                $dispatch('linkaction',{link: '{{route('manage-questions')}}', route: 'manage-questions', fresh: true, fragment: 'page-content'});

                $dispatch('formerrors', {errors: []});
                } else if (typeof $event.detail.content.errors != undefined) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                    } else{
                        $dispatch('formerrors', {errors: $event.detail.content.errors});
                        }
            $el.reset();
        }"
 id="add-question-form" action="" class="  p-2 bg-base-100 rounded-xl max-w-sm lg:max-w-full mx-auto">
    <h1 class="  font-semibold text-secondary">Add new question</h1>

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-1">
        <input type="text" required name="question" placeholder="New Question" class="input input-sm w-full max-w-md bg-base-200 text-base-content font-medium" />
        <button type="submit" class=" btn-sm btn btn-success w-fit">Add question</button>
    </div>
</form>
