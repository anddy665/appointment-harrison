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

        <select name="schedule_id" required>
            <option value="">Select Schedule</option>
            <?php if (!empty($schedules)) : ?>
                <?php foreach ($schedules as $schedule) : ?>
                    <option value="<?= esc_attr($schedule->id); ?>">
                        Date: <?= esc_html($schedule->schedule_date); ?> | Time: <?= esc_html($schedule->start_time); ?> - <?= esc_html($schedule->end_time); ?>
                    </option>
                <?php endforeach; ?>
            <?php else : ?>
                <option value="">No schedules available</option>
            <?php endif; ?>
        </select>

        <button type="submit">Book Appointment</button>
    </form>
</div>