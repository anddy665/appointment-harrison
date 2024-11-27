<?php


require_once APPOINTMENTS_PLUGIN_PATH . 'admin/inc/AppointmentsHandler.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

global $wpdb;
$appointmentHandler = new AppointmentHandler($wpdb);

$appointmentHandler->handleFormSubmission();

$appointments = $appointmentHandler->getAppointments();

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $appointment_to_edit = $appointmentHandler->getAppointmentById($edit_id);
}
?>

<div class="wrap appointments-page">
    <h1>Appointments Page</h1>

    <?php if (isset($appointment_to_edit)): ?>
        <h2>Edit Appointment</h2>
        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?= $appointment_to_edit->id; ?>">

            <div class="form-copule">
                <div class="form-copule-first">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?= esc_attr($appointment_to_edit->full_name); ?>" required>
                </div>

                <div class="form-copule-second">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= esc_attr($appointment_to_edit->email); ?>" required>
                </div>
            </div>


            <div class="form-copule">
                <div class="form-copule-first">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?= esc_attr($appointment_to_edit->phone); ?>" required>

                </div>

                <div class="form-copule-second">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" value="<?= esc_attr(date('Y-m-d', strtotime($appointment_to_edit->appointment_date))); ?>" required>

                </div>
            </div>


            <div class="form-copule">
                <div class="form-copule-first">
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" value="<?= esc_attr($appointment_to_edit->start_time); ?>" required>

                </div>

                <div class="form-copule-second">
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" value="<?= esc_attr($appointment_to_edit->end_time); ?>" required>
                </div>
            </div>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= esc_textarea($appointment_to_edit->description); ?></textarea>

            <div class="form-botton">
                <input type="submit" name="update_appointment" value="Update Appointment">
                <a href="<?= admin_url('admin.php?page=' . MENU_SLUG); ?>" class="button button-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>

    <?php if (!empty($appointments)): ?>
        <h2>Existing Appointments</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Appointment Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                        <td><?= esc_html($appointment->id); ?></td>
                        <td><?= esc_html($appointment->full_name); ?></td>
                        <td><?= esc_html($appointment->email); ?></td>
                        <td><?= esc_html($appointment->phone); ?></td>
                        <td><?= esc_html(date('Y-m-d', strtotime($appointment->appointment_date))); ?></td>
                        <td><?= esc_html($appointment->start_time); ?></td>
                        <td><?= esc_html($appointment->end_time); ?></td>
                        <td><?= esc_html($appointment->description); ?></td>
                        <td>
                            <a href="?page=<?= MENU_SLUG; ?>&action=edit&id=<?= esc_attr($appointment->id); ?>" class="button button-primary">Edit</a>
                            <a href="?page=<?= MENU_SLUG; ?>&action=delete&id=<?= esc_attr($appointment->id); ?>" class="button button-secondary" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="notice notice-info" style="padding: 20px; background-color: #f1f8ff; border-left: 4px solid #0073aa; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px; color: #0073aa;">
                <strong>No appointments found.</strong>
            </p>
        </div>
    <?php endif; ?>
</div>