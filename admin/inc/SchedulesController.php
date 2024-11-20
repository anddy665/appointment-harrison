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
        $schedule_to_edit = $this->getScheduleToEdit();

        switch ($action) {
            case 'edit_schedule':
                $this->editSchedule();
                break;
            case 'delete_schedule':
                $this->deleteSchedule();
                break;
            case 'create_schedule':
                $this->createSchedule();
                break;
        }

        return $schedule_to_edit;
    }

    private function determineAction()
    {
        return isset($_POST['action']) ? sanitize_text_field($_POST['action']) : null;
    }

    private function getScheduleToEdit()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['schedule_id'])) {
            $schedule_id = intval($_GET['schedule_id']);
            return $this->wpdb->get_row(
                $this->wpdb->prepare("SELECT * FROM " . SCHEDULES_TABLE . " WHERE id = %d", $schedule_id)
            );
        }
        return null;
    }

    private function createSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_schedule_nonce_field']) && wp_verify_nonce($_POST['create_schedule_nonce_field'], 'create_schedule_nonce')) {
            $schedule_data = [
                'schedule_date' => sanitize_text_field($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];

            $inserted = $this->wpdb->insert(SCHEDULES_TABLE, $schedule_data);
            if ($inserted === false) {
                error_log('Failed to create schedule: ' . $this->wpdb->last_error);
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
        }
    }

    private function editSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_schedule_nonce_field']) && wp_verify_nonce($_POST['edit_schedule_nonce_field'], 'edit_schedule_nonce')) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_data = [
                'schedule_date' => sanitize_text_field($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];

            $updated = $this->wpdb->update(SCHEDULES_TABLE, $schedule_data, ['id' => $schedule_id]);
            if ($updated === false) {
                error_log('Failed to update schedule: ' . $this->wpdb->last_error);
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
        }
    }

    private function deleteSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_nonce_field']) && wp_verify_nonce($_POST['delete_schedule_nonce_field'], 'delete_schedule_nonce')) {
            $schedule_id = intval($_POST['schedule_id']);
            $deleted = $this->wpdb->delete(SCHEDULES_TABLE, ['id' => $schedule_id]);
            if ($deleted === false) {
                error_log('Failed to delete schedule: ' . $this->wpdb->last_error);
            } else {
                wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
            }
        }
    }

    public function loadAvailableSchedules()
    {
        $schedules = $this->wpdb->get_results("SELECT id, schedule_date, start_time, end_time FROM " . SCHEDULES_TABLE);

        $schedule_hours = [];
        foreach ($schedules as $schedule) {
            $schedule_hours[intval($schedule->schedule_date)] = [
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time
            ];
        }

        return $schedule_hours;
    }
}
