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

class AppointmentsPlugin
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = new AppointmentsDatabaseHandler();

        register_activation_hook(__FILE__, [$this, 'create_appointments_tables']);
        register_deactivation_hook(__FILE__, [$this, 'drop_appointments_tables']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Inyectar la dependencia
        new AdminClass($this->dbHandler);
    }

    public function create_appointments_tables()
    {
        $this->dbHandler->create_tables();
    }

    public function drop_appointments_tables()
    {
        $this->dbHandler->drop_tables();
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook != 'toplevel_page_appointments') {
            return;
        }
        wp_enqueue_style('appointments-admin-style', APPOINTMENTS_PLUGIN_URL . 'admin/assets/style.css');
        wp_enqueue_script('appointments-admin-script', APPOINTMENTS_PLUGIN_URL . 'admin/assets/script.js', array('jquery'), null, true);
    }
}

new AppointmentsPlugin();
