<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
function create_appointments_tables()
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
        description text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;
    ";

    $sql_schedules = "
    CREATE TABLE $table_schedules (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        schedule_time datetime NOT NULL,
        available tinyint(1) NOT NULL DEFAULT 1,
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



    dbDelta($sql_appointments);
    dbDelta($sql_schedules);
    dbDelta($sql_appointments_schedules);
}


function drop_appointments_tables()
{
    global $wpdb;

    $table_appointments = $wpdb->prefix . 'appointments';
    $table_schedules = $wpdb->prefix . 'schedules';
    $table_appointments_schedules = $wpdb->prefix . 'appointments_schedules';

    $sql = "DROP TABLE IF EXISTS $table_appointments_schedules, $table_appointments, $table_schedules;";
    $wpdb->query($sql);
}
