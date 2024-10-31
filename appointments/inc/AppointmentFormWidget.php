<?php

class Appointment_Form_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'appointment_form_widget',
            __('Appointment Form Widget', 'text_domain'),
            array('description' => __('A widget to capture appointment information', 'text_domain'))
        );
    }

    public function widget($args, $instance)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_form_nonce']) && wp_verify_nonce($_POST['appointment_form_nonce'], 'submit_appointment_form')) {
            $this->handle_form_submission();
        }

        echo $args['before_widget'];
        echo '<form id="appointment-form" method="POST">';
        echo wp_nonce_field('submit_appointment_form', 'appointment_form_nonce');
        echo '<input type="text" name="full_name" placeholder="Full Name" required>';
        echo '<input type="email" name="email" placeholder="Email" required>';
        echo '<input type="text" name="phone" placeholder="Phone" required>';
        echo '<input type="datetime-local" name="appointment_date" placeholder="Date" required>';
        echo '<textarea name="description" placeholder="Description"></textarea>';
        echo '<button type="submit">Book Appointment</button>';
        echo '</form>';
        echo $args['after_widget'];
    }

    private function handle_form_submission()
    {
        global $wpdb;

        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $appointment_date = sanitize_text_field($_POST['appointment_date']);
        $description = sanitize_textarea_field($_POST['description']);

        $db_handler = new AppointmentsDatabaseHandler();
        $db_handler->insertAppointment($full_name, $email, $phone, $appointment_date, $description);
    }
}