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
    echo '<div class="wrap">';
    echo '<h1>Schedules Page</h1>';
    echo '<p>This is where you will manage schedules.</p>';
    echo '</div>';
}
