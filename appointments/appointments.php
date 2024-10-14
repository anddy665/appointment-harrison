<?php

/**
 * Plugin Name: Appointments
 * Description: A plugin for managing medical appointments.
 * Version: 1.0
 * Author: Harrisong Gutierrez
 */


require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/admin-functions.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/appointments-functions.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/admin-menu.php';


if (!defined('ABSPATH')) {
    exit;
}

define('APPOINTMENTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('APPOINTMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'create_appointments_tables');
register_deactivation_hook(__FILE__, 'drop_appointments_tables');
