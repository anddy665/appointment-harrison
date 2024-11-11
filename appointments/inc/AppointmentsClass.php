<?php

interface AppointmentDatabaseInterface
{
    public function createTables();
    public function dropTables();
}

class AppointmentDatabaseHandler implements AppointmentDatabaseInterface
{
    public function createTables()
    {
        global $wpdb;

        $table_appointments = $wpdb->prefix . 'appointments';
        $table_schedules = $wpdb->prefix . 'schedules';
        $table_appointments_schedules = $wpdb->prefix . 'appointments_schedules';

        $charset_collate = $wpdb->get_charset_collate();


        $sql_appointments = "
        CREATE TABLE IF NOT EXISTS $table_appointments (
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
        CREATE TABLE IF NOT EXISTS $table_schedules (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            schedule_date TINYINT(1) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;
        ";


        $sql_appointments_schedules = "
        CREATE TABLE IF NOT EXISTS $table_appointments_schedules (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            appointment_id bigint(20) UNSIGNED NOT NULL,
            schedule_id bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (appointment_id) REFERENCES $table_appointments(id) ON DELETE CASCADE,
            FOREIGN KEY (schedule_id) REFERENCES $table_schedules(id) ON DELETE CASCADE
        ) $charset_collate;
        ";


        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_appointments);
        dbDelta($sql_schedules);
        dbDelta($sql_appointments_schedules);
    }

    public function dropTables()
    {
        global $wpdb;

        $table_appointments = $wpdb->prefix . 'appointments';
        $table_schedules = $wpdb->prefix . 'schedules';
        $table_appointments_schedules = $wpdb->prefix . 'appointments_schedules';


        $sql = "DROP TABLE IF EXISTS $table_appointments_schedules, $table_appointments, $table_schedules;";
        $wpdb->query($sql);
    }

    public function insertAppointment($full_name, $email, $phone, $appointment_date, $start_time, $end_time, $description)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'appointments';
        $result = $wpdb->insert(
            $table,
            [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'appointment_date' => $appointment_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'description' => $description,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            error_log('Failed to insert appointment for: ' . $full_name);
            return false;
        }

        return $wpdb->insert_id;
    }
}
