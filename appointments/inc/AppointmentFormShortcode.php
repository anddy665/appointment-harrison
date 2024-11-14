<?php
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AppointmentFormShortcode
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function renderForm()
    {
        $full_name = '';
        $email = '';
        $phone = '';
        $description = '';
        $appointment_date = '';
        $start_time = '';
        $end_time = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_form_nonce']) && wp_verify_nonce($_POST['appointment_form_nonce'], 'submit_appointment_form')) {
            $this->handleFormSubmission($full_name, $email, $phone, $description, $appointment_date, $start_time, $end_time);
        }

        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/appointment-form-template.php';
        return ob_get_clean();
    }

    private function handleFormSubmission(&$full_name, &$email, &$phone, &$description, &$appointment_date, &$start_time, &$end_time)
    {
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $description = sanitize_textarea_field($_POST['description']);
        $appointment_date = sanitize_text_field($_POST['appointment_date']);
        $start_time = sanitize_text_field($_POST['start_time']);
        $end_time = sanitize_text_field($_POST['end_time']);

        $day_of_week = date('w', strtotime($appointment_date));

        $schedule = $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT id FROM {$this->wpdb->prefix}schedules WHERE schedule_date = %d",
            $day_of_week
        ));

        if ($schedule) {
            $schedule_id = $schedule->id;

            $appointment_id = $this->insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description);

            if ($appointment_id) {
                $inserted = $this->wpdb->insert(
                    APPOINTMENTS_SCHEDULES_TABLE,
                    [
                        'appointment_id' => $appointment_id,
                        'schedule_id' => $schedule_id
                    ],
                    ['%d', '%d']
                );

                if ($inserted === false) {
                    error_log('Failed to link appointment ID ' . $appointment_id . ' with schedule ID ' . $schedule_id);
                    return;
                }
                $full_name = '';
                $email = '';
                $phone = '';
                $description = '';
                $appointment_date = '';
                $start_time = '';
                $end_time = '';
            } else {
                error_log('Failed to insert appointment.');
                return;
            }
        } else {
            error_log('No schedule available for the selected date.');
            return;
        }
    }

    public function insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description)
    {

        $table = $this->wpdb->prefix . 'appointments';

        $inserted = $this->wpdb->insert(
            $table,
            [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'appointment_date' => $appointment_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'description' => $description,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($inserted === false) {
            error_log('Failed to insert appointment for: ' . $full_name);
            return false;
        }

        return $this->wpdb->insert_id;
    }
}

new AppointmentFormShortcode();
