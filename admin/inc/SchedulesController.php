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
        $action = $this->determineAction();
        $schedule_to_edit = $this->getScheduleToEdit($action);

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

    private function determineAction()
    {
        return isset($_POST['action']) ? sanitize_text_field($_POST['action']) : 'createSchedule';
    }

    private function getScheduleToEdit($action)
    {
        if ($action === 'editSchedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_to_edit = $this->wpdb->get_row(
                $this->wpdb->prepare("SELECT * FROM " . SCHEDULES_TABLE . " WHERE id = %d", $schedule_id)
            );

            if ($schedule_to_edit === null) {
                error_log('Failed to retrieve schedule with ID ' . $schedule_id);
            }

            return $schedule_to_edit;
        }
        return null;
    }

    private function createSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_date'])) {
            $schedule_data = [
                'schedule_date' => intval($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];

            $inserted = $this->wpdb->insert(SCHEDULES_TABLE, $schedule_data);
            if ($inserted === false) {
                error_log('Failed to create a new schedule.');
                return;
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
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

            $updated = $this->wpdb->update(SCHEDULES_TABLE, $schedule_data, ['id' => $schedule_id]);
            if ($updated === false) {
                error_log('Failed to update the schedule with ID ' . $schedule_id);
                return;
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
        }
    }

    private function deleteSchedule()
    {
        if (isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $deleted = $this->wpdb->delete(SCHEDULES_TABLE, ['id' => $schedule_id]);
            if ($deleted === false) {
                error_log('Failed to delete the schedule with ID ' . $schedule_id);
                return;
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
        }
    }
}
