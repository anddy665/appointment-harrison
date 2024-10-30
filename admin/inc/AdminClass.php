


<?php

require_once 'BaseAdminClass.php';

class AdminClass extends BaseAdminClass
{
    private $dbHandler;

    private const MENU_SLUG = 'appointments';
    private const MENU_TITLE = 'Appointments';
    private const SCHEDULES_SLUG = 'schedules';
    private const SCHEDULES_TITLE = 'Schedules';

    public function __construct(AppointmentsDatabaseInterface $dbHandler)
    {
        $this->dbHandler = $dbHandler;
        add_action('admin_menu', [$this, 'createAdminMenu']);
    }

    public function createAdminMenu()
    {
        // Elimina menús y submenús duplicados si existen
        remove_menu_page(self::MENU_SLUG);
        remove_submenu_page(self::MENU_SLUG, self::SCHEDULES_SLUG);
    
        // Añade el menú principal "Appointments"
        add_menu_page(
            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            self::MENU_SLUG,
            [$this, 'appointmentsPageContent'],
            'dashicons-calendar',
            20
        );
    
        // Añade solo el submenú "Schedules"
        add_submenu_page(
            self::MENU_SLUG,
            self::SCHEDULES_TITLE,
            self::SCHEDULES_TITLE,
            'manage_options',
            'manage_schedules',  // slug único
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
        $table_schedules = $wpdb->prefix . 'schedules';

        $schedule_id = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : null;
        $schedule_date = isset($_POST['schedule_date']) ? sanitize_text_field($_POST['schedule_date']) : null;
        $start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : null;
        $end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : null;

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'edit_schedule':
                    if ($schedule_id && isset($_POST['edit_schedule_nonce_field']) && wp_verify_nonce($_POST['edit_schedule_nonce_field'], 'edit_schedule_nonce')) {
                        $wpdb->update(
                            $table_schedules,
                            ['schedule_date' => $schedule_date, 'start_time' => $start_time, 'end_time' => $end_time],
                            ['id' => $schedule_id]
                        );
                        if ($wpdb->last_error) {
                            $this->showNotice('Failed to update schedule: ' . $wpdb->last_error, 'notice-error');
                        } else {
                            $this->showNotice('Schedule updated successfully.', 'notice-success');
                            wp_redirect(admin_url('admin.php?page=' . self::SCHEDULES_SLUG));
                        }
                    } else {
                        $this->showNotice('Security check failed.', 'notice-error');
                    }
                    break;

                case 'create_schedule':
                    if (isset($_POST['create_schedule_nonce_field']) && wp_verify_nonce($_POST['create_schedule_nonce_field'], 'create_schedule_nonce')) {
                        $wpdb->insert($table_schedules, [
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
                        $wpdb->delete($table_schedules, ['id' => $schedule_id]);
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
                $wpdb->prepare("SELECT * FROM $table_schedules WHERE id = %d", $schedule_id)
            );
        }


        $schedules = $wpdb->get_results("SELECT * FROM $table_schedules");

        $this->loadTemplate('schedules-template', [
            'schedules' => $schedules,
            'schedule_to_edit' => $schedule_to_edit ?? null,
        ]);
    }
}
