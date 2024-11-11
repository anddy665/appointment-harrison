<?php

class BaseAdminClass
{
    protected function loadTemplate($template_name, $args = array())
    {
        $template_path = plugin_dir_path(__FILE__) . '../../admin/templates/' . $template_name . '.php';

        if (file_exists($template_path)) {
            if (!empty($args) && is_array($args)) {
                extract($args);
            } elseif (!empty($args)) {
                error_log('The arguments provided to loadTemplate must be an array. Given: ' . print_r($args, true));
            }
            include $template_path;
        } else {
            error_log('Template file not found: ' . $template_path);
        }
    }
}
