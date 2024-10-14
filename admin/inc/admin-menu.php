<?php


function appointments_admin_menu()
{

    add_menu_page(
        'Appointments',
        'Appointments',
        'manage_options',
        'appointments',
        'appointments_page_content',
        'dashicons-calendar',
        20
    );


    add_submenu_page(
        'appointments',
        'Appointments',
        'Appointments',
        'manage_options',
        'appointments',
        'appointments_page_content'
    );


    add_submenu_page(
        'appointments',
        'Schedules',
        'Schedules',
        'manage_options',
        'schedules',
        'schedules_page_content'
    );
}


add_action('admin_menu', 'appointments_admin_menu');


function appointments_page_content()
{
    echo '<div class="wrap">';
    echo '<h1>Appointments Page</h1>';
    echo '<p>This is where you will manage appointments.</p>';
    echo '</div>';
}




function schedules_page_content()
{
    global $wpdb;
    $table_schedules = $wpdb->prefix . 'schedules';
    $edit_mode = false;
    $schedule_to_edit = null;

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'edit_schedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $schedule_date = sanitize_text_field($_POST['schedule_date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $wpdb->update(
                $table_schedules,
                ['schedule_date' => $schedule_date, 'start_time' => $start_time, 'end_time' => $end_time],
                ['id' => $schedule_id]
            );
            echo '<div class="notice notice-success"><p>Schedule updated successfully.</p></div>';
            echo '<script type="text/javascript">window.location = "' . esc_url(admin_url('admin.php?page=schedules')) . '";</script>';
            exit;
        } elseif ($_POST['action'] == 'create_schedule') {
            $schedule_date = sanitize_text_field($_POST['schedule_date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_time = sanitize_text_field($_POST['end_time']);


            $wpdb->insert($table_schedules, [
                'schedule_date' => $schedule_date,
                'start_time' => $start_time,
                'end_time' => $end_time
            ]);
            echo '<div class="notice notice-success"><p>Schedule created successfully.</p></div>';
        } elseif ($_POST['action'] == 'delete_schedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $wpdb->delete($table_schedules, ['id' => $schedule_id]);
            echo '<div class="notice notice-success"><p>Schedule deleted successfully.</p></div>';
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['schedule_id'])) {
        $schedule_id = intval($_GET['schedule_id']);
        $schedule_to_edit = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_schedules WHERE id = %d", $schedule_id)
        );
        $edit_mode = true;
    }

    echo '<div class="wrap">';
    echo '<h1>Schedules Page</h1>';
    echo '<form method="POST">';
    if ($edit_mode && $schedule_to_edit) {
        echo '<input type="hidden" name="action" value="edit_schedule">';
        echo '<input type="hidden" name="schedule_id" value="' . intval($schedule_to_edit->id) . '">';
        echo '<label for="schedule_date">Date:</label>';
        echo '<input type="date" name="schedule_date" value="' . esc_attr($schedule_to_edit->schedule_date) . '" required>';
        echo '<label for="start_time">Start Time:</label>';
        echo '<input type="time" name="start_time" value="' . esc_attr($schedule_to_edit->start_time) . '" required>';
        echo '<label for="end_time">End Time:</label>';
        echo '<input type="time" name="end_time" value="' . esc_attr($schedule_to_edit->end_time) . '" required>';
        echo '<button type="submit" class="button button-primary">Edit Schedule</button>';
    } else {
        echo '<input type="hidden" name="action" value="create_schedule">';
        echo '<label for="schedule_date">Date:</label>';
        echo '<input type="date" name="schedule_date" required>';
        echo '<label for="start_time">Start Time:</label>';
        echo '<input type="time" name="start_time" required>';
        echo '<label for="end_time">End Time:</label>';
        echo '<input type="time" name="end_time" required>';
        echo '<button type="submit" class="button button-primary">Add Schedule</button>';
    }
    echo '</form>';


    echo '<h2>Existing Schedules</h2>';
    $schedules = $wpdb->get_results("SELECT * FROM $table_schedules");

    if (!empty($schedules)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($schedules as $schedule) {
            echo '<tr>';
            echo '<td>' . intval($schedule->id) . '</td>';
            echo '<td>' . esc_html($schedule->schedule_date) . '</td>';
            echo '<td>' . esc_html($schedule->start_time) . '</td>';
            echo '<td>' . esc_html($schedule->end_time) . '</td>';
            echo '<td>';
            echo '<form method="POST" style="display:inline-block;">';
            echo '<input type="hidden" name="action" value="delete_schedule">';
            echo '<input type="hidden" name="schedule_id" value="' . intval($schedule->id) . '">';
            echo '<button type="submit" class="button button-secondary">Delete</button>';
            echo '</form>';
            echo ' <a href="?page=schedules&action=edit&schedule_id=' . intval($schedule->id) . '" class="button button-primary">Edit</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No schedules available.</p>';
    }
    echo '</div>';
}
