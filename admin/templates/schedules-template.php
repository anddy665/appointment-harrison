<div class="wrap">
    <h1>Schedules Page</h1>

    <?php if (!isset($_GET['action']) || $_GET['action'] !== 'edit'): ?>
        <h2>Add New Schedule</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create_schedule">
            <label for="schedule_date">Date:</label>
            <input type="date" name="schedule_date" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" required>
            <button type="submit" class="button button-primary">Add Schedule</button>
        </form>
    <?php endif; ?>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($schedule_to_edit)): ?>
        <h2>Edit Schedule</h2>
        <form method="POST">
            <input type="hidden" name="action" value="edit_schedule">
            <input type="hidden" name="schedule_id" value="<?= intval($schedule_to_edit->id); ?>">
            <label for="schedule_date">Date:</label>
            <input type="date" name="schedule_date" value="<?= esc_attr($schedule_to_edit->schedule_date); ?>" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" value="<?= esc_attr($schedule_to_edit->start_time); ?>" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" value="<?= esc_attr($schedule_to_edit->end_time); ?>" required>
            <button type="submit" class="button button-primary">Edit Schedule</button>
            <a href="<?= admin_url('admin.php?page=schedules'); ?>" class="button button-secondary">Cancel</a>
        </form>
    <?php endif; ?>

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
                        <td><?= esc_html($schedule->schedule_date); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->start_time))); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->end_time))); ?></td>
                        <td>
                            <a href="?page=schedules&action=edit&schedule_id=<?= intval($schedule->id); ?>" class="button button-primary">Edit</a>
                            <form method="POST" style="display:inline-block;">
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