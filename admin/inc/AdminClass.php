<?php
class AdminClass
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'create_admin_menu']);
    }

    public function create_admin_menu()
    {
        add_menu_page(
            'Appointments',
            'Appointments',
            'manage_options',
            'appointments',
            [$this, 'appointments_page_content'],
            'dashicons-calendar',
            20
        );

        add_submenu_page(
            'appointments',
            'Appointments',
            'Appointments',
            'manage_options',
            'appointments',
            [$this, 'appointments_page_content']
        );

        add_submenu_page(
            'appointments',
            'Schedules',
            'Schedules',
            'manage_options',
            'schedules',
            [$this, 'schedules_page_content']
        );
    }

    public function show_notice($message, $class = 'notice-success')
    {
        $template_path = plugin_dir_path(__FILE__) . '../../admin/templates/notice-template.php';

        if (file_exists($template_path)) {
            $message = sanitize_text_field($message);
            $class = sanitize_html_class($class);
            include $template_path;
        } else {
?>
            <div class="notice notice-error">
                <p>There has been an error</p>
            </div>
        <?php
        }
    }

    public function appointments_page_content()
    {
        ?>
        <div class="wrap">
            <h1>Appointments Page</h1>
            <p>This is where you will manage appointments.</p>
        </div>
<?php
    }

    public function schedules_page_content()
    {
        global $wpdb;
        $table_schedules = $wpdb->prefix . 'schedules';
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
                        if ($wpdb->last_error) {
                            $this->show_notice('Failed to update schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->show_notice('Schedule updated successfully.', 'notice-success');
                            wp_redirect(admin_url('admin.php?page=schedules'));
                        }
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
                    if ($wpdb->last_error) {
                        $this->show_notice('Failed to create schedule: ' . $wpdb->last_error, 'notice-error');
                    } else {
                        $this->show_notice('Schedule created successfully.', 'notice-success');
                    }
                    break;

                case 'delete_schedule':
                    if (isset($_POST['schedule_id'])) {
                        $schedule_id = intval($_POST['schedule_id']);
                        $wpdb->delete($table_schedules, ['id' => $schedule_id]);
                        if ($wpdb->last_error) {
                            $this->show_notice('Failed to delete schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->show_notice('Schedule deleted successfully.', 'notice-success');
                        }
                    }
                    break;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['schedule_id'])) {
            $schedule_id = intval($_GET['schedule_id']);
            $schedule_to_edit = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_schedules WHERE id = %d", $schedule_id)
            );
        }

        $schedules = $wpdb->get_results("SELECT * FROM $table_schedules");

        include($plugin_path . '../templates/schedules-template.php');
    }
}