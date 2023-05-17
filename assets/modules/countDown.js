export function init()
{

    let countdown = document.querySelectorAll('.countdown');

    if (countdown == null) {
        return;
    }

    for (let i = 0; i < countdown.length; i++) {
        let endDate = countdown[i].dataset.countdown,
        daysVal = countdown[i].querySelector('.countdown-days .countdown-value'),
        hoursVal = countdown[i].querySelector('.countdown-hours .countdown-value'),
        minutesVal = countdown[i].querySelector('.countdown-minutes .countdown-value'),
        secondsVal = countdown[i].querySelector('.countdown-seconds .countdown-value'),
        days, hours, minutes, seconds;

        endDate = new Date(endDate).getTime();

        if (isNaN(endDate)) {
            return;
        }

        setInterval(calculate, 1000);

        function calculate()
        {
            let startDate = new Date().getTime();

            let timeRemaining = parseInt((endDate - startDate) / 1000);

            if (timeRemaining >= 0) {
                days = parseInt(timeRemaining / 86400);
                timeRemaining = (timeRemaining % 86400);

                hours = parseInt(timeRemaining / 3600);
                timeRemaining = (timeRemaining % 3600);

                minutes = parseInt(timeRemaining / 60);
                timeRemaining = (timeRemaining % 60);

                seconds = parseInt(timeRemaining);

                if (daysVal != null) {
                    daysVal.innerHTML = parseInt(days, 10);
                }
                if (hoursVal != null) {
                    hoursVal.innerHTML = hours < 10 ? '0' + hours : hours;
                }
                if (minutesVal != null) {
                    minutesVal.innerHTML = minutes < 10 ? '0' + minutes : minutes;
                }
                if (secondsVal != null) {
                    secondsVal.innerHTML = seconds < 10 ? '0' + seconds : seconds;
                }
            }
        }
    }
}
