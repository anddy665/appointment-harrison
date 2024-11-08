<?php

require_once 'BaseController.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class ScheduleController extends BaseController
{
    public function __construct($wpdb)
    {
        parent::__construct($wpdb);
    }

    public function handleRequest()
    {
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : 'createSchedule';
        $schedule_to_edit = null;

        if ($action === 'editSchedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_to_edit = $this->wpdb->get_row(
                $this->wpdb->prepare("SELECT * FROM ".SCHEDULES_TABLE." WHERE id = %d", $schedule_id)
            );
        }

        switch ($action) {
            case 'editSchedule':
                $this->editSchedule();
                break;
            case 'deleteSchedule':
                $this->deleteSchedule();
                break;
            case 'createSchedule':
            default:
                $this->createSchedule();
                break;
        }

        $this->loadTemplate($action, $schedule_to_edit);
    }

    private function createSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_date'])) {
            $schedule_data = [
                'schedule_date' => intval($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];
            $this->wpdb->insert(SCHEDULES_TABLE, $schedule_data);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }

    private function editSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_data = [
                'schedule_date' => intval($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];
            $this->wpdb->update(SCHEDULES_TABLE, $schedule_data, ['id' => $schedule_id]);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }


    private function deleteSchedule()
    {
        if (isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $this->wpdb->delete(SCHEDULES_TABLE, ['id' => $schedule_id]);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }
}
