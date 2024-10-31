<?php

/**
 * Plugin Name: Appointments
 * Description: A plugin for managing medical appointments.
 * Version: 1.0
 * Author: Harrisong Gutierrez
 */

if (!defined('ABSPATH')) {
    exit;
}

define('APPOINTMENTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('APPOINTMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/AdminClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/AppointmentsClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/SchedulesController.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/AppointmentFormWidget.php';

class AppointmentsPlugin
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = new AppointmentsDatabaseHandler();
        $this->register_hooks();
    }

    private function register_hooks()
    {
        register_activation_hook(__FILE__, [$this, 'createAppointmentsTables']);
        register_deactivation_hook(__FILE__, [$this, 'dropAppointmentsTables']);

        add_action('widgets_init', [$this, 'register_appointment_form_widget']);
        add_shortcode('appointment_form', [$this, 'render_appointment_form_shortcode']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);

        new AdminClass($this->dbHandler);
    }


    public function createAppointmentsTables()
    {
        $this->dbHandler->createTables();
    }

    public function dropAppointmentsTables()
    {
        $this->dbHandler->dropTables();
    }

    public function enqueueAdminScripts($hook)
    {
        wp_enqueue_style('appointments-admin-style', APPOINTMENTS_PLUGIN_URL . 'admin/assets/style.css');
        wp_enqueue_script('appointments-admin-script', APPOINTMENTS_PLUGIN_URL . 'admin/assets/js/index.js', array('jquery'), null, true);
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Appointments',
            'Appointments',
            'manage_options',
            'appointments',
            [$this, 'renderAppointmentsPage'],
            'dashicons-calendar-alt',
            6
        );
    }

    public function renderAppointmentsPage()
    {
        global $wpdb;
        $schedules_controller = new SchedulesController($wpdb);
        $schedules_controller->handleRequest();
    }

    public function register_appointment_form_widget()
    {
        register_widget('Appointment_Form_Widget');
    }

    public function render_appointment_form_shortcode()
    {
        ob_start();
        the_widget('Appointment_Form_Widget');
        return ob_get_clean();
    }
}

new AppointmentsPlugin();


// Procesa el formulario del widget y guarda la información en la base de datos
function handle_appointment_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appointment'])) {
        global $wpdb;

        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $appointment_date = sanitize_text_field($_POST['appointment_date']);
        $description = sanitize_textarea_field($_POST['description']);

        $table_appointments = $wpdb->prefix . 'appointments';
        $wpdb->insert($table_appointments, array(
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'appointment_date' => $appointment_date,
            'description' => $description
        ));

        echo '<p>Cita agendada correctamente</p>';
    }
}
add_action('init', 'handle_appointment_form_submission');