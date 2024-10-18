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

class Appointments_Plugin {
    
    public function __construct() {
        
        register_activation_hook(__FILE__, [$this, 'create_appointments_tables']);
        register_deactivation_hook(__FILE__, [$this, 'drop_appointments_tables']);
        
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        
        
        new Admin_Class();
    }

    
    public function create_appointments_tables() {
        Appointments_Class::create_tables();
    }

   
    public function drop_appointments_tables() {
        Appointments_Class::drop_tables();
    }

    
    public function enqueue_admin_scripts($hook) {
        wp_enqueue_script(
            'appointments-admin-script',
            APPOINTMENTS_PLUGIN_URL . 'admin/assets/js/index.js',
            array('jquery'),
            '1.0',
            true
        );
    }
}


if (class_exists('Appointments_Plugin')) {
    $appointments_plugin = new Appointments_Plugin();
}
