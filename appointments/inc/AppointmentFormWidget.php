<?php

class AppointmentFormWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'AppointmentFormWidget',
            __('Appointment Form Widget', 'text_domain'),
            array('description' => __('A widget to capture appointment information', 'text_domain'))
        );
    }

    public function widget($args, $instance)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_form_nonce']) && wp_verify_nonce($_POST['appointment_form_nonce'], 'submit_appointment_form')) {
            $this->handle_form_submission();
        }

?>
        <?= $args['before_widget']; ?>
        <form id="appointment-form" method="POST">
            <?php wp_nonce_field('submit_appointment_form', 'appointment_form_nonce'); ?>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <textarea name="description" placeholder="Description"></textarea>

            <?php
            global $wpdb;
            $schedules = $wpdb->get_results("SELECT id, schedule_date, start_time, end_time FROM {$wpdb->prefix}schedules");
            ?>
            <select name="schedule_id" required>
                <option value="">Select Schedule</option>
                <?php foreach ($schedules as $schedule) : ?>
                    <option value="<?= $schedule->id; ?>">Date: <?= $schedule->schedule_date; ?> | Time: <?= $schedule->start_time; ?> - <?= $schedule->end_time; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Book Appointment</button>
        </form>
        <?= $args['after_widget']; ?>
<?php
    }

    private function handle_form_submission()
    {
        global $wpdb;

        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $description = sanitize_textarea_field($_POST['description']);
        $schedule_id = intval($_POST['schedule_id']);

        $schedule = $wpdb->get_row("SELECT schedule_date, start_time FROM {$wpdb->prefix}schedules WHERE id = $schedule_id");

        $appointment_id = $this->insertAppointment($full_name, $email, $phone, "{$schedule->schedule_date} {$schedule->start_time}", $description);

        $wpdb->insert(
            "{$wpdb->prefix}appointments_schedules",
            [
                'appointment_id' => $appointment_id,
                'schedule_id' => $schedule_id
            ],
            ['%d', '%d']
        );
    }

    private function insertAppointment($full_name, $email, $phone, $appointment_date, $description)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'appointments';
        $wpdb->insert(
            $table,
            [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'appointment_date' => $appointment_date,
                'description' => $description,
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );

        return $wpdb->insert_id;
    }
}
