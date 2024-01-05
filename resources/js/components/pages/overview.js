import axios from "axios";
import Chart from "chart.js/auto";
export default () => ({
    selectedCenter : null,
    isSearchResults : false,
    journal: null,
    chartCanvas : null,
    validChartCanvas : null,
    genuineChartCanvas : null,
    processChartData : [],
    validChartData : [],
    genuineChartData : [],
    selectedMonth : null,
    journalSubmit(formID, url, route) {
        let formdata = new FormData(document.getElementById(formID));
        if(this.journal != null){
            let currentBody = this.journal.body;
            let newBody = currentBody+"\n"+formdata.get('body');
            formdata.set('body',newBody);
        }
        this.$dispatch("formsubmit", {
            url: url,
            route: route,
            fragment: "page-content",
            formData: formdata,
            target: formID,
        });
    },
    postJournalSubmission(content) {
        if (content.success == true) {
            this.$dispatch("showtoast", {
                message: content.message,
                mode: "success",
            });
            if(content.journal != null && content.journal != undefined){
                if(this.journal == null){
                    this.journal = content.journal;
                }else{
                    this.journal.body = content.journal.body;
                }
            }
        } else if (content.success == false) {
            this.$dispatch("showtoast", {
                message: content.message,
                mode: "error",
            });
        }
    },
    getDate() {
        var today = new Date();
        var monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        var day = today.getDate();
        var monthIndex = today.getMonth();
        var year = today.getFullYear();
        var formattedDay = day < 10 ? "0" + day : day;
        var monthName = monthNames[monthIndex];
        var formattedDate = formattedDay + " " + monthName + " " + year;

        return formattedDate;
    },
    getMonth(){

        const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

        let currentUrl = window.location.href;
        let url = new URL(currentUrl)
        if(url.searchParams.has('month')){
            var dateObj = new Date(url.searchParams.get('month') + "-01");
            return monthNames[dateObj.getMonth()];
        }
          const currentDate = new Date();
          const currentMonthIndex = currentDate.getMonth();
          const currentMonth = monthNames[currentMonthIndex];

          return currentMonth;
    },
    initChart(){
        new Chart(this.chartCanvas, {
            type: 'pie',
            data: {
                labels: ['Unprocessed', 'Follow-up Started', 'Appointment Scheduled', 'Consulted', 'Closed'],
                datasets: [
                  {
                    label: 'Lead Management Process',
                    data: [ this.processChartData.unprocessed_leads, this.processChartData.followed_up_leads, this.processChartData.appointments_created, this.processChartData.consulted, this.processChartData.closed ],
                    backgroundColor: [ "#FF9D76", "#FCDDB0",
                    "#51EAEA", "#82CD47", "#FB3569" ],
                  }
                ]
              },
            options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: true,
                    position: 'bottom',
                  },
                  title: {
                    display: true,
                    text: 'Process Overview - '+this.getMonth()
                  }
                }
              }
          });

          new Chart(this.validChartCanvas, {
            type: 'pie',
            data: {
                labels: ['Valid leads', 'Non validated / Invalid leads'],
                datasets: [
                  {
                    label: 'Lead Validation',
                    data: [this.validChartData.valid_leads, this.validChartData.invalid_leads],
                    backgroundColor: [ "#51EAEA", "#FB3569"],
                  }
                ]
              },
            options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: true,
                    position: 'bottom',
                  },
                  title: {
                    display: true,
                    text: 'Leads Validation - '+this.getMonth()
                  }
                }
              }
          });


          new Chart(this.genuineChartCanvas, {
            type: 'pie',
            data: {
                labels: ['Genuine Leads', 'Non checked / False leads'],
                datasets: [
                  {
                    label: 'Lead Genuinity',
                    data: [this.genuineChartData.genuine_leads,this.genuineChartData.false_leads],
                    backgroundColor: [ "#51EAEA", "#FB3569"],
                  }
                ]
              },
            options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: true,
                    position: 'bottom',
                  },
                  title: {
                    display: true,
                    text: 'Leads Genuineness Check - '+this.getMonth()
                  }
                }
              }
          });
    },
    searchPerformance(el){
        let formdata = new FormData(el);
        let from = formdata.get('from');
        let to = formdata.get('to');
        let center = formdata.get('center');
        let params = {
            from: from,
            to: to
        };
        if(center != null && center != ''){
            params.center = center;
        }
        this.$dispatch('linkaction',{link:'/performance', route: 'performance',fragment:'page-content',params:params});
    },
    getParams(){
        let queryString = window.location.search;
        let params = new URLSearchParams(queryString);
        this.selectedMonth = params.get('month');
    },
    resetPerformancePage(){
        this.$dispatch('linkaction',{link:'/performance', route: 'performance',fragment:'page-content', fresh: true});
    },
    checkForParams(){
        let queryString = window.location.search;
        let params = new URLSearchParams(queryString);
        if(params.size != 0){
            this.isSearchResults = true;
        }
    }
});
