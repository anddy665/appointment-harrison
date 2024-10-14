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
    $table_appointments = $wpdb->prefix . 'appointments';

    $edit_mode = false;
    $schedule_to_edit = null;


    if (isset($_POST['action'])) {


        if (isset($_POST['schedule_time'])) {
            $schedule_time = sanitize_text_field($_POST['schedule_time']);
        }


        if ($_POST['action'] == 'edit_schedule' && isset($_POST['schedule_id'])) {
            $schedule_id = intval($_POST['schedule_id']);
            $wpdb->update(
                $table_schedules,
                ['schedule_time' => $schedule_time],
                ['id' => $schedule_id]
            );
            echo '<div class="notice notice-success"><p>Schedule updated successfully.</p></div>';


            $_POST = [];
            $edit_mode = false;
            $schedule_to_edit = null;


            echo '<script type="text/javascript">
                window.location = "' . esc_url(admin_url('admin.php?page=schedules')) . '";
            </script>';
            exit;
        } elseif ($_POST['action'] == 'create_schedule') {

            $schedule_date = date('Y-m-d', strtotime($schedule_time));


            $existing_schedule = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_appointments WHERE DATE(appointment_date) = %s",
                    $schedule_date
                )
            );

            if ($existing_schedule > 0) {
                echo '<div class="notice notice-error"><p>Error: A schedule already exists for this date.</p></div>';
            } else {
                $wpdb->insert($table_schedules, [
                    'schedule_time' => $schedule_time,
                    'available' => 1
                ]);
                echo '<div class="notice notice-success"><p>Schedule created successfully.</p></div>';
            }
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
        echo '<label for="schedule_time">Edit Date and Time:</label>';
        echo '<input type="datetime-local" name="schedule_time" value="' . date('Y-m-d\TH:i', strtotime($schedule_to_edit->schedule_time)) . '" required>';
        echo '<button type="submit" class="button button-primary">Edit Schedule</button>';
    } else {

        echo '<input type="hidden" name="action" value="create_schedule">';
        echo '<label for="schedule_time">Select Date and Time:</label>';
        echo '<input type="datetime-local" name="schedule_time" required>';
        echo '<button type="submit" class="button button-primary">Add Schedule</button>';
    }
    echo '</form>';


    echo '<h2>Existing Schedules</h2>';
    $schedules = $wpdb->get_results("SELECT * FROM $table_schedules");

    if (!empty($schedules)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Schedule Time</th>';
        echo '<th>Available</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($schedules as $schedule) {
            echo '<tr>';
            echo '<td>' . intval($schedule->id) . '</td>';
            echo '<td>' . esc_html($schedule->schedule_time) . '</td>';
            echo '<td>' . ($schedule->available ? 'Yes' : 'No') . '</td>';
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

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No schedules available.</p>';
    }

    echo '</div>';
}
