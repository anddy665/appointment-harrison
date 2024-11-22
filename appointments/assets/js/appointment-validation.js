document.addEventListener("DOMContentLoaded", function () {

    const scheduleHours = typeof scheduleHoursData !== 'undefined' ? scheduleHoursData : {};
    const appointmentDateInput = document.getElementById('appointment_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');


    function clearError(inputElement) {
        inputElement.classList.remove('input-error');
    }


    function showErrorMessage(message) {
        if (errorText && errorMessage) {
            errorText.textContent = message;
            errorMessage.style.display = 'flex';
        }
    }


    function hideErrorMessage() {
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
        if (errorText) {
            errorText.textContent = '';
        }
    }

    appointmentDateInput.addEventListener('change', function () {
        clearError(this);
        hideErrorMessage();
        const selectedDate = new Date(this.value);
        const selectedDay = selectedDate.getUTCDay();

        const availableDay = scheduleHours[selectedDay];

        if (availableDay) {
            clearError(startTimeInput);
            clearError(endTimeInput);

            startTimeInput.addEventListener('change', function () {
                clearError(this);
                hideErrorMessage();

                const userStartTime = this.value;
                const userEndTime = endTimeInput.value;

                if (userStartTime < availableDay.start_time || (userEndTime && userEndTime > availableDay.end_time)) {
                    this.classList.add('input-error');
                    showErrorMessage(
                        "Selected time is outside of the available range for this date. Please select a time between " +
                        availableDay.start_time + " and " + availableDay.end_time
                    );
                    this.value = '';
                }
            });


            endTimeInput.addEventListener('change', function () {
                clearError(this);
                hideErrorMessage();

                const userEndTime = this.value;
                const userStartTime = startTimeInput.value;

                if ((userStartTime && userStartTime < availableDay.start_time) || userEndTime > availableDay.end_time) {
                    this.classList.add('input-error');
                    showErrorMessage(
                        "Selected time is outside of the available range for this date. Please select a time between " +
                        availableDay.start_time + " and " + availableDay.end_time
                    );
                    this.value = '';
                }
            });
        } else {
            this.classList.add('input-error');
            showErrorMessage("Selected date is not available for appointments. Please choose another date.");
            this.value = '';
        }
    });
});
