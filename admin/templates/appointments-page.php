<?php

require_once plugin_dir_path(__FILE__) . '../inc/AppointmentsHandler.php';


AppointmentsHandler::handleFormSubmission();

$appointments = AppointmentsHandler::getAppointments();

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $appointment_to_edit = AppointmentsHandler::getAppointmentById($edit_id);
}
?>

<div class="wrap">
    <h1>Appointments Page</h1>

    <?php if (isset($appointment_to_edit)): ?>
        <h2>Edit Appointment</h2>
        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?= $appointment_to_edit->id; ?>">

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= esc_attr($appointment_to_edit->full_name); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= esc_attr($appointment_to_edit->email); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= esc_attr($appointment_to_edit->phone); ?>" required>

            <label>Appointment Date:</label>
            <input type="date" name="appointment_date" value="<?= esc_attr(date('Y-m-d', strtotime($appointment_to_edit->appointment_date))); ?>" required>

            <label>Start Time:</label>
            <input type="time" name="start_time" value="<?= esc_attr($appointment_to_edit->start_time); ?>" required>

            <label>End Time:</label>
            <input type="time" name="end_time" value="<?= esc_attr($appointment_to_edit->end_time); ?>" required>

            <label>Description:</label>
            <textarea name="description" required><?= esc_textarea($appointment_to_edit->description); ?></textarea>

            <input type="submit" name="update_appointment" value="Update Appointment">
            <a href="<?= admin_url('admin.php?page=appointments'); ?>" class="button">Cancel</a>
        </form>
    <?php endif; ?>

    <?php if (!empty($appointments)): ?>
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
                            <a href="?page=appointments&action=edit&id=<?= esc_attr($appointment->id); ?>" class="button">Edit</a>
                            <a href="?page=appointments&action=delete&id=<?= esc_attr($appointment->id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No appointments found.</p>
    <?php endif; ?>
</div>