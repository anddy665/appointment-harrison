<?php
if (!defined('ABSPATH')) {
    exit;
}

$schedule_to_edit = isset($args['schedule_to_edit']) ? $args['schedule_to_edit'] : null;
$schedules = isset($args['schedules']) ? $args['schedules'] : [];
?>

<div class="wrap schedules-page">
    <h1>Schedules Page</h1>

    <h2><?= $schedule_to_edit ? 'Edit Schedule' : 'Add New Schedule'; ?></h2>

    <form method="POST">
        <?php wp_nonce_field($schedule_to_edit ? 'edit_schedule_nonce' : 'create_schedule_nonce', $schedule_to_edit ? 'edit_schedule_nonce_field' : 'create_schedule_nonce_field'); ?>
        <input type="hidden" name="action" value="<?= $schedule_to_edit ? 'edit_schedule' : 'create_schedule'; ?>">
        <?php if ($schedule_to_edit): ?>
            <input type="hidden" name="schedule_id" value="<?= intval($schedule_to_edit->id); ?>">
        <?php endif; ?>

        <label for="schedule_date">Day of the Week:</label>
        <select name="schedule_date" required>
            <option value="0" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 0 ? 'selected' : ''; ?>>Sunday</option>
            <option value="1" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 1 ? 'selected' : ''; ?>>Monday</option>
            <option value="2" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 2 ? 'selected' : ''; ?>>Tuesday</option>
            <option value="3" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 3 ? 'selected' : ''; ?>>Wednesday</option>
            <option value="4" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 4 ? 'selected' : ''; ?>>Thursday</option>
            <option value="5" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 5 ? 'selected' : ''; ?>>Friday</option>
            <option value="6" <?= isset($schedule_to_edit) && $schedule_to_edit->schedule_date == 6 ? 'selected' : ''; ?>>Saturday</option>
        </select>


        <div class="time-inputs">
            <div class="time-input-first">
                <label for="start_time">Start Time:</label>
                <input type="time" name="start_time" value="<?= isset($schedule_to_edit) ? esc_attr($schedule_to_edit->start_time) : ''; ?>" required>
            </div>

            <div class="time-input-second">
                <label for="end_time">End Time:</label>
                <input type="time" name="end_time" value="<?= isset($schedule_to_edit) ? esc_attr($schedule_to_edit->end_time) : ''; ?>" required>
            </div>
        </div>


        <button type="submit" class="button button-primary">
            <?= $schedule_to_edit ? 'Edit Schedule' : 'Add Schedule'; ?>
        </button>

        <?php if ($schedule_to_edit): ?>
            <a href="<?= admin_url('admin.php?page=schedules'); ?>" class="button button-secondary">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>Existing Schedules</h2>
    <?php if (!empty($schedules)): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= intval($schedule->id); ?></td>
                        <td><?= esc_html(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$schedule->schedule_date]); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->start_time))); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->end_time))); ?></td>
                        <td>
                            <a href="?page=schedules&action=edit&schedule_id=<?= intval($schedule->id); ?>" class="button button-primary">Edit</a>
                            <form method="POST" style="display:inline-block;">
                                <?php wp_nonce_field('delete_schedule_nonce', 'delete_schedule_nonce_field'); ?>
                                <input type="hidden" name="action" value="delete_schedule">
                                <input type="hidden" name="schedule_id" value="<?= intval($schedule->id); ?>">
                                <button type="submit" class="button button-secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="notice notice-info" style="padding: 20px; background-color: #f1f8ff; border-left: 4px solid #0073aa; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px; color: #0073aa;">
                <strong>No schedules available.</strong>
            </p>
        </div>
    <?php endif; ?>
</div>