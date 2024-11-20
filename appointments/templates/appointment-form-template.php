<?php
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
    <div id="error-message" class="error-message" style="display: none;">
        <span id="error-text"></span>
    </div>
</div>