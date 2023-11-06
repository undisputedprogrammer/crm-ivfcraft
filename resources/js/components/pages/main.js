import axios from "axios";

export default () => ({
    sendingMessages: false,
    loading: false,
    selectedLeads: {},
    isBreak: false,
    selectedCenter: null,
    selectLead(el, lead) {
        if (el.checked) {
            this.selectedLeads[lead.id] = lead.phone;
        } else {
            delete this.selectedLeads[lead.id];
        }

        console.log(Object.keys(this.selectedLeads).length);
    },
    selectAll(el) {
        let checkboxes = document.getElementsByClassName(
            "individual-checkboxes"
        );
        for (let i = 0; i < checkboxes.length; i++) {
            if (el.checked == true) {
                if (checkboxes[i].checked != true) {
                    checkboxes[i].click();
                }
            } else {
                if (checkboxes[i].checked != false) {
                    checkboxes[i].click();
                }
            }
        }
    },
    sendBulkMessage(ajaxLoading) {
        if (Object.keys(this.selectedLeads).length < 1) {
            console.log("No leads selected");
        } else {
            this.sendingMessages = true;
            setTimeout(() => {
                axios
                    .post("/message/bulk/sent", {
                        numbers: this.selectedLeads,
                        template:
                            document.getElementById("selectTemplate").value,
                    })
                    .then((r) => {
                        console.log(r.data);
                        this.sendingMessages = false;
                        this.$dispatch("showtoast", {
                            mode: "success",
                            message: "Messages scheduled for sending",
                        });
                        this.selectedLeads = {};
                    })
                    .catch((e) => {
                        console.log(e);
                        this.sendingMessages = false;
                        this.$dispatch("showtoast", {
                            mode: "error",
                            message:
                                "Messages not sent, please try after some time.",
                        });
                    });
            }, 500);
        }
    },
    setBreakStartTime(url) {
        this.loading = true;

        setTimeout(() => {
            axios
                .get(url)
                .then((r) => {
                    console.log(r.data);
                    if (r.data.success == true) {
                        this.isBreak = true;
                    } else {
                        this.$dispatch("showtoast", {
                            message: r.data.message,
                            mode: "error",
                        });
                    }
                })
                .catch((e) => {
                    console.log(e);
                });
            this.loading = false;
        }, 500);
    },

    filterByCenter(el, link) {
        let center = document.getElementById("select-center");
        this.selectedCenter = center.value;
        // let fromdata = new FormData(el);
        if (this.selectedCenter == "all") {
            this.$dispatch("linkaction", {
                link: link,
                route: link,
                fragment: "page-content",
            });
        } else {
            this.$dispatch("linkaction", {
                link: link,
                route: link,
                fragment: "page-content",
                params: { center: this.selectedCenter },
            });
        }
    },
    fetchLatest(latest) {
        if (latest == null) {
            axios
                .get("/fetch/latest")
                .then((r) => {
                    console.log("fetch latest response is ");
                    console.log(r.data);
                    latest = r.data;
                })
                .catch((c) => {
                    console.log("Could not fetch latest");
                });
        }
        return latest;
    },
    escapeSingleQuotes(inputString) {
        return inputString.replace(/'/g, "'");
    },
    getDateWithoutTime(inputDate) {
        const [year, month, day] = inputDate.split("-");

        const months = [
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

        return `${day} ${months[parseInt(month) - 1]} ${year}`;
    },
    getPage() {
        let currentUrl = window.location.href;
        let pageUrl = new URL(currentUrl);
        if (pageUrl.searchParams.has("page")) {
            return pageUrl.searchParams.get("page");
        } else {
            return null;
        }
    },
    updateDateFormat(inputDatestr) {
        var parts = inputDatestr.split("-");
        var inputDate = new Date(parts[2], parts[1] - 1, parts[0]);
        var outputDateStr =
            inputDate.getFullYear() +
            "-" +
            String(inputDate.getMonth() + 1).padStart(2, "0") +
            "-" +
            String(inputDate.getDate()).padStart(2, "0") +
            " 00:00:00";
        console.log(outputDateStr);
        return outputDateStr;
    },
    formatDateOnly(inputDate) {
        let date = new Date(inputDate);
        if (isNaN(date.getTime())) {
            inputDate = this.updateDateFormat(inputDate);
            date = new Date(inputDate);
        }

        const months = [
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
        const monthAbbreviation = months[date.getMonth()];
        const day = date.getDate();
        const year = date.getFullYear();

        return `${monthAbbreviation} ${day} ${year}`;
        }

});
