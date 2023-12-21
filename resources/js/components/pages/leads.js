import axios from "axios";
export default ()=>({
    followup_remarks : [],
    selected : false,
    showTemplateModal : false,
    selectedCenter : null,
    selectedAgent: null,
    selectedStatus : null,
    theLink : null,
    is_genuine : null,
    is_valid : null,
    creation_date_from : null,
    creation_date_to: null,
    segment : null,
    campaign : null,
    source : null,
    isProcessed : false,
    call_status : null,
    editLead : false,
    createLead : false,
    showImage : false,
    referLead : false,
    search : null,
    tableHeading : 'Showing fresh leads',
    sources : [],
    c : null,
    campaigns : [],
    container : document.getElementById('autocomplete-items'),
    matches : [],
    autoComplete : false,
    toggleTemplateModal(){
        this.showTemplateModal = !this.showTemplateModal;
    },
    selectTemplate(el){
        let formdata = new FormData(el);
        console.log(formdata);
    },
    searchlead(){
        let searchString = document.getElementById('search-input').value;
        let status = 'all';
        this.$dispatch('linkaction',{link: this.theLink, route:'fresh-leads',fragment:'page-content',fresh: true, params:{search: searchString, status: status}});
    },
    filterLead(el){
        let formdata = new FormData(el);
        let is_valid = formdata.get('is_valid');
        let is_genuine = formdata.get('is_genuine');
        let center = formdata.get('center');
        let agent = formdata.get('agent');
        let status = formdata.get('status');
        let segment = formdata.get('segment');
        let campaign = formdata.get('campaign');
        let source = formdata.get('source');
        let creation_date_from = formdata.get('creation_date_from');
        let creation_date_to = formdata.get('creation_date_to');
        let call_status = formdata.get('call_status');

        let filter = {};
        if(is_valid != ''){
            filter.is_valid = is_valid;
        }
        if(is_genuine != ''){
            filter.is_genuine = is_genuine;
        }
        if(center != 'all'){
            filter.center = center;
        }
        if(agent != 'all'){
            filter.agent = agent;
        }
        if(status != '' && status != 'none'){
            filter.status = status;
        }
        if(segment != ''){
            filter.segment = segment;
        }
        if(campaign != ''){
            filter.campaign = campaign;
        }
        if(source != ''){
            filter.source = source;
        }
        if(call_status != null && call_status != ''){
            filter.call_status = call_status;
        }
        if(creation_date_from != null && creation_date_from != ''){
            filter.creation_date_from = creation_date_from;
        }
        if(creation_date_to != null && creation_date_to != ''){
            filter.creation_date_to = creation_date_to;
        }

        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params: filter});

    },
    filterByStatus(el){
        let formdata = new FormData(el);
        let status = formdata.get('status');
        let is_valid = formdata.get('is_valid');
        let is_genuine = formdata.get('is_genuine');
        let params = {};
        if(status != null && status != ''){
            params.status = status;
        }
        if(is_valid != null && is_valid != ''){
            is_valid = (is_valid === 'true');
            params.is_valid = is_valid;
        }
        if(is_genuine != null && is_genuine != ''){
            is_genuine = (is_genuine === 'true');
            params.is_genuine = is_genuine;
        }
        let centerEl = document.getElementById('select-center');
        if (centerEl) {
            let selectedCenter = centerEl.value;
            params.center = selectedCenter;
        }
        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params: params});
    },
    filterByCreationDate(el){
        let formdata = new FormData(el);
        let creation_date = formdata.get('creation_date');
        let params = {
            creation_date : creation_date
        };

        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params: params});
    },
    leadsProcessedToday(){
        let params = {
            processed : true
        };
        this.$dispatch('linkaction',{link: this.theLink, route: 'fresh-leads', fragment: 'page-content', fresh: true, params: params});
    },
    setIsProcessed(){
        let link = new URL(window.location.href);
        let processed = url.searchParams.get('processed');
        console.log(processed);
        if(processed == true){
            this.isProcessed = processed;
        }
    },
    updateLead(el, lead_id, url){
        let formdata = new FormData(el);
        formdata.append('lead_id', lead_id);
        this.$dispatch('formsubmit',{url: url, route: 'lead.update',fragment: 'page-content', formData: formdata, target: 'lead-edit-form'});
    },
    storeLead(el, url){
        let formdata = new FormData(el);
        this.$dispatch('formsubmit', {url: url, route: 'lead.store', fragment: 'page-content', formData: formdata, target: 'create-lead-form'});
    },
    referNewLead(el, url){
        let formdata = new FormData(el);
        this.$dispatch('formsubmit', {url: url, route: 'lead.refer', fragment: 'page-content', formData: formdata, target: 'refer-lead-form'});
    },
    getTableHeading(){
        if(window.location.search.length > 0){
            this.tableHeading = 'Showing search results';
        }
        const params = new URLSearchParams(window.location.search);
        if(params.has('processed')){
            this.tableHeading="Showing leads processed today";
        }
    },
    loadSources(link){
        axios.get(link)
        .then( (response) => {
            this.sources = response.data.sources;
        })
        .catch(function (error) {
            console.log(error);
        });
    },
    setCampaignsArray(campaignObject){
        campaignObject = JSON.parse(campaignObject.replace(/&quot;/g, '"'));
        this.campaigns = new Array();
        campaignObject.forEach((c) => {
            this.campaigns.push(c.name)
        });
    },
    campaignInputChanged(value){
        let campaigns = Array.from(this.campaigns);
        let matches = [];
        let i = 0;
        this.autoComplete = false;
        campaigns.forEach((c)=>{
            if(c.toLowerCase().includes(value.toLowerCase())){
                matches[i] = c;
                i++;
            }
        })

        if(matches.length > 0 ){
            this.autoComplete = true;
        }

        return matches;
    },
    completeInput(input, inputId){
        document.getElementById(inputId).value = input;
        this.autoComplete = false;
    }
});
