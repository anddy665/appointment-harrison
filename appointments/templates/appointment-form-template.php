<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$schedules = $wpdb->get_results("SELECT id, schedule_date, start_time, end_time FROM {$wpdb->prefix}schedules");
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

<script>
    document.getElementById('appointment_date').addEventListener('change', function() {

        const selectedDate = new Date(this.value);
        const selectedDay = selectedDate.getUTCDay();


        const availableDays = <?php echo json_encode(array_map('intval', array_column($schedules, 'schedule_date'))); ?>;

        console.log("Días disponibles:", availableDays);
        console.log("Día seleccionado (UTC):", selectedDay);

        const isMatch = availableDays.includes(selectedDay);

        if (isMatch) {
            console.log("Día coincidente encontrado:", selectedDay);

        } else {
            alert("Selected date is not available for appointments. Please choose another date.");
            this.value = '';
        }
    });
</script>