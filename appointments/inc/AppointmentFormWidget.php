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
            $this->handleFormSubmission();
        }

        echo $args['before_widget'];


        include plugin_dir_path(__FILE__) . '../templates/appointment-form-template.php';

        echo $args['after_widget'];
    }


    private function handleFormSubmission()
    {
        global $wpdb;
        
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $description = sanitize_textarea_field($_POST['description']);
        $appointment_date = sanitize_text_field($_POST['appointment_date']);  // Obtén la fecha seleccionada por el usuario
        $start_time = sanitize_text_field($_POST['start_time']);  // Obtén la hora de inicio seleccionada
        $end_time = sanitize_text_field($_POST['end_time']);  // Obtén la hora de fin seleccionada
    
        // Get the day of the week from the appointment date
        $day_of_week = date('w', strtotime($appointment_date)); // 0 (Sunday) to 6 (Saturday)
    
        // Fetch the corresponding schedule_id for that day
        $schedule = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}schedules WHERE schedule_date = %d",
            $day_of_week
        ));
    
        // Check if a schedule exists for that day
        if ($schedule) {
            $schedule_id = $schedule->id;
    
            // Call to insert the appointment
            $appointment_id = $this->insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description);
    
            // Insert into the appointments_schedules table
            $wpdb->insert(
                "{$wpdb->prefix}appointments_schedules",
                [
                    'appointment_id' => $appointment_id,
                    'schedule_id' => $schedule_id
                ],
                ['%d', '%d']
            );
        } else {
            // Handle case where no schedule exists for the selected date
            // You might want to set an error message to inform the user
            // or handle this case as per your requirements
            error_log('No schedule available for the selected date.');
        }
    }
    
    

    public function insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description)
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
                'start_time' => $start_time,
                'end_time' => $end_time,
                'description' => $description,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );
    
        return $wpdb->insert_id;
    }
    
}
