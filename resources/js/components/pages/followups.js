export default () => ({
    theLink : null,
    fpselected : false,
    lead : [],
    fp : [],
    qnas: [],
    fps : [],
    isValid : false,
    fpname : '',
    fpremarks : [],
    isGenuine : false,
    historyLoading: true,
    leadremarks: [],
    convert: false,
    fphistory: [],
    appointment: null,
    consult: false,
    page: null,
    showconsultform: false,
    showImage: false,
    selectedCenter : null,
    selectedAgent : null,
    is_genuine : null,
    is_valid : null,
    creation_date_from : null,
    creation_date_to : null,
    call_status : null,
    segment : null,
    campaign : null,
    source : null,
    search : null,
    filterLead(el,link){
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
        if(creation_date_from != null && creation_date_from != ''){
            filter.creation_date_from = creation_date_from;
        }
        if(creation_date_to != null && creation_date_to != ''){
            filter.creation_date_to = creation_date_to;
        }
        if(call_status != null && call_status != ''){
            filter.call_status = call_status;
        }

        this.$dispatch('linkaction',{link: link, route: 'followups', fragment: 'page-content', fresh: true, params: filter});

    },

    searchlead(){
        let searchString = document.getElementById('search-input').value;

        this.$dispatch('linkaction',{link: this.theLink, route:'followups',fragment:'page-content',fresh: true, params:{search: searchString}});
    }
})
