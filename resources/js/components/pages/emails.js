export default () => ({
    lead : null,
    sendCustomMail(el, url){
        let formdata = new FormData(el);
        formdata.append('lead_id',this.lead.id);
        this.$dispatch('formsubmit',{url:url, route: 'email.send',fragment: 'page-content', formData: formdata, target: 'custom-email-form'});
    },
    postFormResponse(event, el){
        if(event.success == true){
            el.reset();
            this.$dispatch('showtoast',{mode: 'success',message:'Email sent successfully!'});
        }
        else if(event.success == false){
            this.$dispatch('showtoast',{mode: 'error',message:'Could not sent email'});
        }
        else if (typeof event.errors != undefined) {
            this.$dispatch('showtoast', {message: event.message, mode: 'error'});

        } else{
            this.$dispatch('formerrors', {errors: event.errors});
        }
    }
});
