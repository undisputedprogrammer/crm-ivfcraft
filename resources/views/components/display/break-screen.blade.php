<div x-show="isBreak == true" x-cloak x-transition class=" w-full h-screen bg-black absolute top-0  left-0 flex flex-col justify-center items-center z-50">
    <h1 class=" text-xl text-center w-full font-bold text-base-content py-5">Break time</h1>
    <form x-data="{
        error : '',
        doSubmit() {
            let form = document.getElementById('break-out-form');
            let formdata = new FormData(form);
            $dispatch('formsubmit',{url:'{{route('break.out')}}', route: 'break.out',fragment: 'page-content', formData: formdata, target: 'break-out-form'});
        }
    }"
    @formresponse.window="
    if($event.detail.target == $el.id){
        if ($event.detail.content.success) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                isBreak = $event.detail.content.isBreak;
                $dispatch('formerrors', {errors: []});
            } else if (typeof $event.detail.content.errors != undefined) {
                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});
                error = $event.detail.content.message;
            } else{
                $dispatch('formerrors', {errors: $event.detail.content.errors});
            }
        $el.reset();
    }"
    id="break-out-form" @submit.prevent.stop="doSubmit()" action="" class=" flex flex-col items-start space-y-2">
        <input type="password" name="current_password" required class=" input input-primary text-base-content min-w-72" placeholder="Enter password to continue">
        <p class=" text-error font-medium" x-text="error"></p>
        <button type="submit" class=" btn btn-primary w-fit btn-sm">End Break</button>
    </form>
</div>
