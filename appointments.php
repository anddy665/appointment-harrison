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
        add_action('wp_enqueue_scripts', [$this, 'enqueueAppointmentScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAppointmentStyles']);
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
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
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

    public function enqueueAdminAssets($hook)
    {

        wp_enqueue_script(
            'appointments-admin-script',
            APPOINTMENTS_PLUGIN_URL . 'admin/assets/js/index.js',
            array('jquery'),
            null,
            true
        );


        wp_enqueue_style(
            'appointments-admin-style',
            APPOINTMENTS_PLUGIN_URL . 'admin/assets/css/main.css',
            array(),
            null
        );
    }


    public function enqueueAppointmentScripts()
    {
        global $wpdb;
        $schedule_controller = new ScheduleController($wpdb);
        $schedule_hours = $schedule_controller->loadAvailableSchedules();

        wp_register_script('appointment-validation-script', APPOINTMENTS_PLUGIN_URL . 'appointments/assets/js/appointment-validation.js', array('jquery'), null, true);
        wp_localize_script('appointment-validation-script', 'scheduleHoursData', $schedule_hours);
        wp_enqueue_script('appointment-validation-script');
    }

    public function enqueueAppointmentStyles()
    {
        wp_enqueue_style('appointment-main-style', APPOINTMENTS_PLUGIN_URL . 'appointments/assets/css/main.css');
    }

    public function renderAppointmentFormShortcode()
    {
        $shortcodeHandler = new AppointmentFormShortcode();
        return $shortcodeHandler->renderForm();
    }
}

new AppointmentPlugin();
