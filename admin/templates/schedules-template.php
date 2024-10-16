<div class="wrap">
    <h1>Schedules Page</h1>
    <form method="POST">
        <?php if ($edit_mode && $schedule_to_edit): ?>
            <input type="hidden" name="action" value="edit_schedule">
            <input type="hidden" name="schedule_id" value="<?= intval($schedule_to_edit->id); ?>">
            <label for="schedule_date">Date:</label>
            <input type="date" name="schedule_date" value="<?= esc_attr($schedule_to_edit->schedule_date); ?>" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" value="<?= esc_attr($schedule_to_edit->start_time); ?>" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" value="<?= esc_attr($schedule_to_edit->end_time); ?>" required>
            <button type="submit" class="button button-primary">Edit Schedule</button>
        <?php else: ?>
            <input type="hidden" name="action" value="create_schedule">
            <label for="schedule_date">Date:</label>
            <input type="date" name="schedule_date" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" required>
            <button type="submit" class="button button-primary">Add Schedule</button>
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
                        <td><?= esc_html($schedule->schedule_date); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->start_time))); ?></td>
                        <td><?= esc_html(date('h:i A', strtotime($schedule->end_time))); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="action" value="delete_schedule">
                                <input type="hidden" name="schedule_id" value="<?= intval($schedule->id); ?>">
                                <button type="submit" class="button button-secondary">Delete</button>
                            </form>
                            <a href="?page=schedules&action=edit&schedule_id=<?= intval($schedule->id); ?>" class="button button-primary">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No schedules available.</p>
    <?php endif; ?>
</div>