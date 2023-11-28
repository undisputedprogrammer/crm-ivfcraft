<span
@changequestion.window="
        if($event.detail.current == $event.detail.q_answer){
            console.log('cannot change answer');
        }
        else{

        ajaxLoading = true;
        axios.get($event.detail.link,{
            params: {
                lead_id : lead.id,
                q_answer : $event.detail.q_answer,
                question : $event.detail.question
            }
        }).then(function(response){
            if(response.data.q_visit != undefined){
                if(response.data.q_visit == null || response.data.q_visit == 'null'){
                    lead.q_visit = null;
                }
                else{
                    lead.q_visit = response.data.q_visit;
                }
            }
            if(response.data.q_decide != undefined){
                if(response.data.q_decide == null || response.data.q_decide == ''){
                    lead.q_decide = null;
                }
                else{
                    lead.q_decide = response.data.q_decide;
                }
            }
            lead.customer_segment = response.data.customer_segment;
            if(fp != undefined){
                fp.lead = lead;
                fps[fp.id] = fp;
            }
            document.getElementById('segment-'+lead.id).innerText = lead.customer_segment;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
            document.getElementById('segment-'+lead.id).innerText = lead.customer_segment;
        }).catch(function(error){
            console.log(error);
            ajaxLoading = false;
        });
        }" >
</span>
