<?php

class BaseController {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    protected function loadTemplate($action, $schedule_to_edit) {
        $schedules = $this->getSchedules();
        get_template_part('templates/schedules-template', null, [
            'action' => $action,
            'schedule_to_edit' => $schedule_to_edit,
            'schedules' => $schedules,
        ]);
    }

    protected function getSchedules() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}schedules");
    }
}
