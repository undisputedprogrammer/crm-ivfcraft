<span
@pageaction.window="
        params = {};
        selectedCenter !== null && (params.center = selectedCenter);

        if(selectedStatus != null && selectedStatus != 'none'){
            params.status = selectedStatus;
        }

        is_valid !== null && (params.is_valid = is_valid);

        is_genuine !== null && (params.is_genuine = is_genuine);

        selectedAgent !== null && (params.agent = selectedAgent);

        creation_date_from !== null && (params.creation_date_from = creation_date_from);

        creation_date_to !== null && (params.creation_date_to = creation_date_to);

        if(isProcessed != undefined){
            isProcessed == true && (params.processed = true);
        }

        segment !== null && (params.segment = segment);

        campaign !== null && (params.campaign = campaign);

        source !== null && (params.source = source);

        search !== null && (params.search = search);

        call_status !== null && (params.call_status = call_status);

        if(Object.keys(params).length > 0){
            details = {
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
                params: params
            };
        }else{
            details = {
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content'
            };
        }

        $dispatch('linkaction', details);
        ">

</span>
