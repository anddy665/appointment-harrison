<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$schedules = $wpdb->get_results("SELECT id, schedule_date, start_time, end_time FROM {$wpdb->prefix}schedules");

$schedule_hours = [];
foreach ($schedules as $schedule) {
    $schedule_hours[intval($schedule->schedule_date)] = [
        'start_time' => $schedule->start_time,
        'end_time' => $schedule->end_time
    ];
}
?>

<div class="wrap">
    <h1>Book an Appointment</h1>

    <form id="appointment-form" method="POST">
        <?php wp_nonce_field('submit_appointment_form', 'appointment_form_nonce'); ?>
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <textarea name="description" placeholder="Description"></textarea>

        <label for="appointment_date">Select Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" required>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required>

        <button type="submit">Book Appointment</button>
    </form>
</div>

<style>
    .input-error {
        border: 2px solid red!important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const scheduleHours = <?php echo json_encode($schedule_hours); ?>;
        
        const appointmentDateInput = document.getElementById('appointment_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        function clearError(inputElement) {
            inputElement.classList.remove('input-error');
        }

        appointmentDateInput.addEventListener('change', function() {
            clearError(this);

            const selectedDate = new Date(this.value);
            const selectedDay = selectedDate.getUTCDay();

            const availableDay = scheduleHours[selectedDay];

            if (availableDay) {
               
                clearError(startTimeInput);
                clearError(endTimeInput);

                startTimeInput.addEventListener('change', function () {
                    clearError(this); 
                    const userStartTime = this.value;
                    const userEndTime = endTimeInput.value;

                    if (userStartTime < availableDay.start_time || (userEndTime && userEndTime > availableDay.end_time)) {
                        this.classList.add('input-error'); 
                        alert("Selected time is outside of the available range for this date. Please select a time between " + availableDay.start_time + " and " + availableDay.end_time);
                        this.value = '';
                    }
                });

                endTimeInput.addEventListener('change', function () {
                    clearError(this); 
                    const userEndTime = this.value;
                    const userStartTime = startTimeInput.value;

                    if ((userStartTime && userStartTime < availableDay.start_time) || userEndTime > availableDay.end_time) {
                        this.classList.add('input-error'); 
                        alert("Selected time is outside of the available range for this date. Please select a time between " + availableDay.start_time + " and " + availableDay.end_time);
                        this.value = ''; 
                    }
                });
            } else {
                this.classList.add('input-error');
                alert("Selected date is not available for appointments. Please choose another date.");
                this.value = ''; 
            }
        });
    });
</script>
