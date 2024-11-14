<?php

require_once 'BaseAdminClass.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AdminClass extends BaseAdminClass
{
    private $dbHandler;


    private const MENU_TITLE = 'Appointments';
    private const SCHEDULES_TITLE = 'Schedules';

    public function __construct(AppointmentDatabaseInterface $dbHandler)
    {
        $this->dbHandler = $dbHandler;
        add_action('admin_menu', [$this, 'createAdminMenu']);
    }

    public function createAdminMenu()
    {
        add_menu_page(

            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            MENU_SLUG,
            [$this, 'appointmentsPageContent'],
            'dashicons-calendar',
            20
        );

        add_submenu_page(
            MENU_SLUG,
            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            MENU_SLUG,
            [$this, 'appointmentsPageContent']
        );

        add_submenu_page(
            MENU_SLUG,
            self::SCHEDULES_TITLE,
            self::SCHEDULES_TITLE,
            'manage_options',
            SCHEDULES_SLUG,
            [$this, 'schedulesPageContent']
        );
    }

    public function showNotice($message, $class = 'notice-success')
    {
        $args = array(
            'message' => $message,
            'class' => $class,
        );
        $this->loadTemplate('notice-template', $args);
    }

    public function appointmentsPageContent()
    {
        $this->loadTemplate('appointments-page');
    }

    public function schedulesPageContent()
    {
        global $wpdb;


        $schedule_id = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : null;
        $schedule_date = isset($_POST['schedule_date']) ? sanitize_text_field($_POST['schedule_date']) : null;
        $start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : null;
        $end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : null;

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'edit_schedule':
                    if ($schedule_id && isset($_POST['edit_schedule_nonce_field']) && wp_verify_nonce($_POST['edit_schedule_nonce_field'], 'edit_schedule_nonce')) {
                        $wpdb->update(
                            SCHEDULES_TABLE,
                            ['schedule_date' => $schedule_date, 'start_time' => $start_time, 'end_time' => $end_time],
                            ['id' => $schedule_id]
                        );
                        if ($wpdb->last_error) {
                            $this->showNotice('Failed to update schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->showNotice('Schedule updated successfully.', 'notice-success');
                            wp_redirect(admin_url('admin.php?page=' . SCHEDULES_SLUG));
                        }
                    } else {
                        $this->showNotice('Security check failed.', 'notice-error');
                    }
                    break;

                case 'create_schedule':
                    if (isset($_POST['create_schedule_nonce_field']) && wp_verify_nonce($_POST['create_schedule_nonce_field'], 'create_schedule_nonce')) {
                        $wpdb->insert(SCHEDULES_TABLE, [
                            'schedule_date' => $schedule_date,
                            'start_time' => $start_time,
                            'end_time' => $end_time
                        ]);
                        if ($wpdb->last_error) {
                            $this->showNotice('Failed to create schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->showNotice('Schedule created successfully.', 'notice-success');
                        }
                    } else {
                        $this->showNotice('Security check failed.', 'notice-error');
                    }
                    break;

                case 'delete_schedule':
                    if ($schedule_id && isset($_POST['delete_schedule_nonce_field']) && wp_verify_nonce($_POST['delete_schedule_nonce_field'], 'delete_schedule_nonce')) {
                        $wpdb->delete(SCHEDULES_TABLE, ['id' => $schedule_id]);
                        if ($wpdb->last_error) {
                            $this->showNotice('Failed to delete schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->showNotice('Schedule deleted successfully.', 'notice-success');
                        }
                    } else {
                        $this->showNotice('Security check failed.', 'notice-error');
                    }
                    break;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['schedule_id'])) {
            $schedule_id = intval($_GET['schedule_id']);
            $schedule_to_edit = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM " . SCHEDULES_TABLE . " WHERE id = %d", $schedule_id)
            );
            
        }


        $schedules = $wpdb->get_results("SELECT * FROM " . SCHEDULES_TABLE);


        $this->loadTemplate('schedules-template', [
            'schedules' => $schedules,
            'schedule_to_edit' => $schedule_to_edit ?? null,
        ]);
    }
}
