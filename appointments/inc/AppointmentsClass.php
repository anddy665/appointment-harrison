<?php

require_once APPOINTMENTS_PLUGIN_PATH . 'appointments/interfaces/AppointmentDatabaseInterface.php';
require_once APPOINTMENTS_PLUGIN_PATH . 'config.php';

class AppointmentDatabaseHandler implements AppointmentDatabaseInterface
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function createTables()
    {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql_appointments = "
        CREATE TABLE IF NOT EXISTS " . APPOINTMENTS_TABLE . " (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            full_name varchar(255) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            appointment_date datetime NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            description text NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;
        ";

        $sql_schedules = "
        CREATE TABLE IF NOT EXISTS " . SCHEDULES_TABLE . " (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            schedule_date TINYINT(1) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;
        ";

        $sql_appointments_schedules = "
        CREATE TABLE IF NOT EXISTS " . APPOINTMENTS_SCHEDULES_TABLE . " (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            appointment_id bigint(20) UNSIGNED NOT NULL,
            schedule_id bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (appointment_id) REFERENCES " . APPOINTMENTS_TABLE . "(id) ON DELETE CASCADE,
            FOREIGN KEY (schedule_id) REFERENCES " . SCHEDULES_TABLE . "(id) ON DELETE CASCADE
        ) $charset_collate;
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_appointments);
        dbDelta($sql_schedules);
        dbDelta($sql_appointments_schedules);
    }

    public function dropTables()
    {
        $sql = "DROP TABLE IF EXISTS " . APPOINTMENTS_SCHEDULES_TABLE . ", " . APPOINTMENTS_TABLE . ", " . SCHEDULES_TABLE . ";";
        $this->wpdb->query($sql);
    }
}
