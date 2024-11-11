<?php
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AppointmentHandler
{
    public static function handleFormSubmission()
    {
        global $wpdb;

        if (isset($_POST['update_appointment'])) {
            $edit_id = intval($_POST['edit_id']);
            $full_name = sanitize_text_field($_POST['full_name']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $appointment_date = sanitize_text_field($_POST['appointment_date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $description = sanitize_textarea_field($_POST['description']);

            $updated = $wpdb->update(
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
            } else {
                wp_redirect(admin_url('admin.php?page=appointments'));
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $delete_id = intval($_GET['id']);
            $deleted = $wpdb->delete(APPOINTMENTS_TABLE, ['id' => $delete_id], ['%d']);

            if ($deleted === false) {
                error_log('An error occurred while deleting the appointment with ID ' . $delete_id);
            } else {
                wp_redirect(admin_url('admin.php?page=appointments'));
            }
        }
    }

    public static function getAppointments()
    {
        global $wpdb;
        $appointments = $wpdb->get_results("SELECT * FROM " . APPOINTMENTS_TABLE);

        if ($appointments === false) {
            error_log('An error occurred while retrieving the appointments.');
        }

        return $appointments;
    }

    public static function getAppointmentById($id)
    {
        global $wpdb;
        $appointment = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . APPOINTMENTS_TABLE . " WHERE id = %d", $id));

        if ($appointment === null) {
            error_log('No appointment found with ID ' . $id);
        }

        return $appointment;
    }
}
