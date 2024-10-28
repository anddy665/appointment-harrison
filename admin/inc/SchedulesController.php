<?php

class SchedulesController {
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function handleRequest() {
  
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : 'createSchedule';
        $schedule_to_edit = null;

        
        if ($action === 'editSchedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_to_edit = $this->wpdb->get_row(
                $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}schedules WHERE id = %d", $schedule_id)
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

    private function loadTemplate($action, $schedule_to_edit) {
        $schedules = $this->getSchedules();
        get_template_part('templates/schedules-template', null, [
            'action' => $action,
            'schedule_to_edit' => $schedule_to_edit,
            'schedules' => $schedules,
        ]);
    }

    private function getSchedules() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}schedules");
    }

    private function createSchedule() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_date'])) {
            $schedule_data = [
                'schedule_date' => sanitize_text_field($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];
            $this->wpdb->insert("{$this->wpdb->prefix}schedules", $schedule_data);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }

    private function editSchedule() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_data = [
                'schedule_date' => sanitize_text_field($_POST['schedule_date']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
            ];
            $this->wpdb->update("{$this->wpdb->prefix}schedules", $schedule_data, ['id' => $schedule_id]);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }

    private function deleteSchedule() {
        if (isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $this->wpdb->delete("{$this->wpdb->prefix}schedules", ['id' => $schedule_id]);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }
}
