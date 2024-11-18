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

require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/AdminClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/AppointmentsClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/SchedulesController.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/inc/AppointmentFormShortcode.php';

class AppointmentPlugin
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = new AppointmentDatabaseHandler();
        $this->registerHooks();
    }

    private function registerHooks()
    {
        $this->registerActivationHooks();
        $this->registerShortcodes();
        $this->registerAdminHooks();
        new AdminClass($this->dbHandler);
    }


    private function registerActivationHooks()
    {
        register_activation_hook(__FILE__, [$this, 'createAppointmentsTables']);
        register_deactivation_hook(__FILE__, [$this, 'dropAppointmentsTables']);
    }

    private function registerShortcodes()
    {
        add_shortcode('appointment_form', [$this, 'renderAppointmentFormShortcode']);
    }

    private function registerAdminHooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
    }



    public function createAppointmentsTables()
    {
        try {
            $this->dbHandler->createTables();
        } catch (Exception $e) {
            error_log('Error creating appointment tables: ' . $e->getMessage());
        }
    }

    public function dropAppointmentsTables()
    {
        try {
            $this->dbHandler->dropTables();
        } catch (Exception $e) {
            error_log('Error dropping appointment tables: ' . $e->getMessage());
        }
    }

    public function enqueueAdminScripts($hook)
    {
        wp_enqueue_style('appointments-admin-style', APPOINTMENTS_PLUGIN_URL . 'admin/assets/style.css');
        wp_enqueue_script('appointments-admin-script', APPOINTMENTS_PLUGIN_URL . 'admin/assets/js/index.js', array('jquery'), null, true);
    }


    public function renderAppointmentsPage()
    {
        global $wpdb;
        $schedules_controller = new ScheduleController($wpdb);
        $schedules_controller->handleRequest();
    }


    public function renderAppointmentFormShortcode()
    {
        $shortcodeHandler = new AppointmentFormShortcode();
        return $shortcodeHandler->renderForm();
    }
}

new AppointmentPlugin();
