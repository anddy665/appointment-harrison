<?php

require_once  APPOINTMENTS_PLUGIN_PATH . 'common/LoadTemplateClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AdminClass extends BaseLoadTemplateClass
{
    private $dbHandler;

    private const MENU_TITLE = 'Appointments';
    private const SCHEDULES_TITLE = 'Schedules';

    public function __construct(AppointmentDatabaseInterface $dbHandler)
    {
        $this->dbHandler = $dbHandler;
        add_action('admin_menu', [$this, 'createAdminMenu']);
    }

    public function createAdminMenu()
    {
        add_menu_page(

            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            MENU_SLUG,
            [$this, 'appointmentsPageContent'],
            'dashicons-calendar',
            20
        );

        add_submenu_page(
            MENU_SLUG,
            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            MENU_SLUG,
            [$this, 'appointmentsPageContent']
        );

        add_submenu_page(
            MENU_SLUG,
            self::SCHEDULES_TITLE,
            self::SCHEDULES_TITLE,
            'manage_options',
            SCHEDULES_SLUG,
            [$this, 'schedulesPageContent']
        );
    }

    public function showNotice($message, $class = 'notice-success')
    {
        $args = array(
            'message' => $message,
            'class' => $class,
        );
        $this->loadTemplate('notice-template', $args);
    }

    public function appointmentsPageContent()
    {
        $this->loadTemplate('appointments-page');
    }

    public function schedulesPageContent()
    {
        global $wpdb;

        $scheduleController = new ScheduleController($wpdb);

        $schedule_to_edit = $scheduleController->handleRequest();

        $schedules = $wpdb->get_results("SELECT * FROM " . SCHEDULES_TABLE);

        $this->loadTemplate('schedules-template', [
            'schedules' => $schedules,
            'schedule_to_edit' => $schedule_to_edit,
        ]);
    }
}
