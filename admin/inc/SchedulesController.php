<?php

class SchedulesController {
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function handle_request() {
        // Determinar acción
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : 'create_schedule';
        $schedule_to_edit = null;

        // Cargar el horario a editar si corresponde
        if ($action === 'edit_schedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_to_edit = $this->wpdb->get_row(
                $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}schedules WHERE id = %d", $schedule_id)
            );
        }

        // Manejar acciones de creación, edición y eliminación
        switch ($action) {
            case 'edit_schedule':
                $this->edit_schedule();
                break;
            case 'delete_schedule':
                $this->delete_schedule();
                break;
            case 'create_schedule':
            default:
                $this->create_schedule();
                break;
        }

        // Cargar el template
        $this->load_template($action, $schedule_to_edit);
    }

    private function load_template($action, $schedule_to_edit) {
        $schedules = $this->get_schedules();
        get_template_part('templates/schedules-template', null, [
            'action' => $action,
            'schedule_to_edit' => $schedule_to_edit,
            'schedules' => $schedules,
        ]);
    }

    private function get_schedules() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}schedules");
    }

    private function create_schedule() {
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

    private function edit_schedule() {
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

    private function delete_schedule() {
        if (isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $this->wpdb->delete("{$this->wpdb->prefix}schedules", ['id' => $schedule_id]);
            wp_redirect(admin_url('admin.php?page=schedules'));
            exit;
        }
    }
}
