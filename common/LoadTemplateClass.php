<?php

class BaseLoadTemplateClass
{
    protected function loadTemplate($template_name, $args = array())
    {
        if (empty($template_name) || !is_string($template_name)) {
            error_log('Invalid template name provided to loadTemplate.');
            return;
        }

        $admin_template_path = plugin_dir_path(__FILE__) . '../admin/templates/' . $template_name . '.php';
        $appointments_template_path = plugin_dir_path(__FILE__) . '../appointments/templates/' . $template_name . '.php';

        switch (true) {
            case file_exists($admin_template_path):
                $template_path = $admin_template_path;
                break;
            case file_exists($appointments_template_path):
                $template_path = $appointments_template_path;
                break;
            default:
                error_log('Template file not found: ' . $template_name);
                return;
        }

        if (file_exists($template_path)) {
            if (!empty($args) && is_array($args)) {
                extract($args, EXTR_SKIP);
            } elseif (!empty($args)) {
                error_log('The arguments provided to loadTemplate must be an array. Given: ' . print_r($args, true));
            }
            include $template_path;
        }
    }
}
