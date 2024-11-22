<?php

require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'common/LoadTemplateClass.php';

class AppointmentFormShortcode extends BaseLoadTemplateClass
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function renderForm()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_form_nonce']) && wp_verify_nonce($_POST['appointment_form_nonce'], 'submit_appointment_form')) {
            $this->handleFormSubmission();
        }

        ob_start();
        $this->loadTemplate('appointment-form-template');
        return ob_get_clean();
    }

    private function handleFormSubmission()
    {

        $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : null;
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : null;
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : null;
        $appointment_date = isset($_POST['appointment_date']) ? sanitize_text_field($_POST['appointment_date']) : null;
        $start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : null;
        $end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : null;


        if ($full_name && $email && $appointment_date && $start_time && $end_time) {
            $day_of_week = date('w', strtotime($appointment_date));

            $schedule = $this->wpdb->get_row($this->wpdb->prepare(
                'SELECT id FROM ' . SCHEDULES_TABLE . ' WHERE schedule_date = %d',
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
                } else {
                    error_log('Failed to insert appointment.');
                    return;
                }
            } else {
                error_log('No schedule available for the selected date.');
                return;
            }
        } else {
            error_log('Required form fields are missing.');
            return;
        }
    }

    public function insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description)
    {
        $table = APPOINTMENTS_TABLE;

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
