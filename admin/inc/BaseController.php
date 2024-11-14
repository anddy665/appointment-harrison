<?php
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';
class BaseController

{
    protected $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    protected function loadTemplate($action, $schedule_to_edit)
    {
        $schedules = $this->getSchedules();

        if ($schedules === false) {
            error_log('Failed to retrieve schedules from the database.');
            return;
        }

        get_template_part('templates/schedules-template', null, [
            'action' => $action,
            'schedule_to_edit' => $schedule_to_edit,
            'schedules' => $schedules,
        ]);
    }

    protected function getSchedules()
    {
        $results = $this->wpdb->get_results("SELECT * FROM ". SCHEDULES_SLUG);

        if ($results === false) {
            error_log('Database query failed in getSchedules.');
            return [];
        }

        return $results;
    }
}
