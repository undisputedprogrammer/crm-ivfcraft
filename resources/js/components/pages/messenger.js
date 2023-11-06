export default () => ({

    convertTime(timestamp){
        let date = new Date(timestamp);

        let today = new Date();

        let hours = date.getHours();
        let amOrpm = 'AM'
        if(hours > 12){
            amOrpm = 'PM';
            hours = hours % 12 || 12;
        }
        let minutes = date.getMinutes();

        if(date.getDate() == today.getDate() && date.getMonth() == today.getMonth() && date.getFullYear() == today.getFullYear())
        {
            return `${hours}:${minutes.toLocaleString('en-US', { minimumIntegerDigits: 2 })} ${amOrpm}`;
        }else{
            let day = date.getDate();
            let month = date.toLocaleString('en-US',{month:'short'});
            return `${month} ${day} ${hours}:${minutes.toLocaleString('en-US', { minimumIntegerDigits: 2 })} ${amOrpm}`;
        }
    }
})
