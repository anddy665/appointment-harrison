<?php

interface AppointmentsDatabaseInterface
{
    public function createTables();
    public function dropTables();
}


class AppointmentsDatabaseHandler implements AppointmentsDatabaseInterface
{
    public function createTables()
    {
        global $wpdb;

        $table_appointments = $wpdb->prefix . 'appointments';
        $table_schedules = $wpdb->prefix . 'schedules';
        $table_appointments_schedules = $wpdb->prefix . 'appointments_schedules';

        $charset_collate = $wpdb->get_charset_collate();

        $sql_appointments = "
        CREATE TABLE $table_appointments (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        full_name varchar(255) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        appointment_date datetime NOT NULL,
        start_time time NOT NULL,       -- Nueva columna
        end_time time NOT NULL,         -- Nueva columna
        description text NOT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;
        ";

        $sql_schedules = "
        CREATE TABLE $table_schedules (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        schedule_date TINYINT(1) NOT NULL, -- Aquí cambiamos a TINYINT para almacenar el número del día
        start_time time NOT NULL,
        end_time time NOT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;
        ";

        $sql_appointments_schedules = "
        CREATE TABLE $table_appointments_schedules (
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
        $wpdb->insert(
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
    
        return $wpdb->insert_id;
    }
    
}
