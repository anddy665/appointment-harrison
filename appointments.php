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

class AppointmentsPlugin
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = new AppointmentsDatabaseHandler();

        register_activation_hook(__FILE__, [$this, 'createAppointmentsTables']);
        register_deactivation_hook(__FILE__, [$this, 'dropAppointmentsTables']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('admin_menu', [$this, 'addAdminMenu']); 

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
        $schedules_controller->handle_request(); 
    }
}

new AppointmentsPlugin();
