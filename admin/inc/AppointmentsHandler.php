<?php
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AppointmentHandler extends BaseLoadTemplateClass
{
    private $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }



    public function handleFormSubmission()
    {
        $message = '';
        $class = '';
        if (isset($_POST['update_appointment']) && !empty($_POST['edit_id'])) {
            $this->updateAppointment();
            $message = 'Appointment updated successfully!';
            $class = 'notice-success';
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
            $this->deleteAppointment();
            $message = 'Appointment deleted successfully!';
            $class = 'notice-error';
        }

        if (!empty($message) && !empty($class)) {
            $this->loadTemplate('notice-template', ['message' => $message, 'class' => $class]);
        }
    }


    private function updateAppointment()
    {
        $edit_id = intval($_POST['edit_id']);
        if ($edit_id <= 0) {
            error_log('Invalid appointment ID for update.');
            return;
        }

        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $appointment_date = sanitize_text_field($_POST['appointment_date']);
        $start_time = sanitize_text_field($_POST['start_time']);
        $end_time = sanitize_text_field($_POST['end_time']);
        $description = sanitize_textarea_field($_POST['description']);

        $updated = $this->wpdb->update(
            APPOINTMENTS_TABLE,
            [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'appointment_date' => $appointment_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'description' => $description
            ],
            ['id' => $edit_id],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            error_log('An error occurred while updating the appointment with ID ' . $edit_id);
            return;
        } elseif ($updated === 0) {
            error_log('No changes made while updating the appointment with ID ' . $edit_id);
            return;
        } else {
            wp_redirect(admin_url('admin.php?page=' . MENU_SLUG));
        }
    }

    private function deleteAppointment()
    {
        $delete_id = intval($_GET['id']);
        if ($delete_id <= 0) {
            error_log('Invalid appointment ID for deletion.');
            return;
        }

        $deleted = $this->wpdb->delete(APPOINTMENTS_TABLE, ['id' => $delete_id], ['%d']);

        if ($deleted === false) {
            error_log('An error occurred while deleting the appointment with ID ' . $delete_id);
            return;
        } elseif ($deleted === 0) {
            error_log('No appointment found to delete with ID ' . $delete_id);
            return;
        } else {
            wp_redirect(admin_url('admin.php?page=' . MENU_SLUG));
        }
    }

    public function getAppointments()
    {
        $appointments = $this->wpdb->get_results("SELECT * FROM " . APPOINTMENTS_TABLE);

        if ($appointments === false) {
            error_log('An error occurred while retrieving the appointments.');
            return [];
        }

        return $appointments ?: [];
    }

    public function getAppointmentById($id)
    {
        $appointment = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . APPOINTMENTS_TABLE . " WHERE id = %d", $id));

        if ($appointment === null) {
            error_log('No appointment found with ID ' . $id);
            return null;
        }

        return $appointment;
    }
}
