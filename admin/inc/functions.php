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
?>
    <div class="wrap">
        <h1>Appointments Page</h1>
        <p>This is where you will manage appointments.</p>
    </div>
    <?php
}


function schedules_page_content()
{
    global $wpdb;
    $table_schedules = $wpdb->prefix . 'schedules';
    $edit_mode = false;
    $schedule_to_edit = null;
    $plugin_path = plugin_dir_path(__FILE__);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit_schedule':
                if (isset($_POST['schedule_id'])) {
                    $schedule_id = intval($_POST['schedule_id']);
                    $schedule_date = sanitize_text_field($_POST['schedule_date']);
                    $start_time = sanitize_text_field($_POST['start_time']);
                    $end_time = sanitize_text_field($_POST['end_time']);
                    $wpdb->update(
                        $table_schedules,
                        ['schedule_date' => $schedule_date, 'start_time' => $start_time, 'end_time' => $end_time],
                        ['id' => $schedule_id]
                    );

                    wp_redirect(admin_url('admin.php?page=schedules'));
                }
                break;

            case 'create_schedule':
                $schedule_date = sanitize_text_field($_POST['schedule_date']);
                $start_time = sanitize_text_field($_POST['start_time']);
                $end_time = sanitize_text_field($_POST['end_time']);
                $wpdb->insert($table_schedules, [
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]);
    ?>
                <div class="notice notice-success">
                    <p>Schedule created successfully.</p>
                </div>
                <?php
                break;

            case 'delete_schedule':
                if (isset($_POST['schedule_id'])) {
                    $schedule_id = intval($_POST['schedule_id']);
                    $wpdb->delete($table_schedules, ['id' => $schedule_id]);
                ?>
                    <div class="notice notice-success">
                        <p>Schedule deleted successfully.</p>
                    </div>
<?php
                }
                break;
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['schedule_id'])) {
        $schedule_id = intval($_GET['schedule_id']);
        $schedule_to_edit = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_schedules WHERE id = %d", $schedule_id)
        );
        $edit_mode = true;
    }

    $schedules = $wpdb->get_results("SELECT * FROM $table_schedules");

    include($plugin_path . '../templates/schedules-template.php');
}
