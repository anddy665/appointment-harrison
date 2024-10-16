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

require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/functions.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/appointments-functions.php';
// require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/admin-menu.php';


register_activation_hook(__FILE__, 'create_appointments_tables');
register_deactivation_hook(__FILE__, 'drop_appointments_tables');





function appointments_admin_enqueue_scripts($hook)
{

    wp_enqueue_script(
        'appointments-admin-script',
        APPOINTMENTS_PLUGIN_URL . 'admin/assets/js/index.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'appointments_admin_enqueue_scripts');
