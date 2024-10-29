<?php

class BaseAdminClass
{
    protected function loadTemplate($template_name, $args = array())
    {
        $template_path = plugin_dir_path(__FILE__) . '../../admin/templates/' . $template_name . '.php';

        if (file_exists($template_path)) {
            if (!empty($args) && is_array($args)) {
                extract($args);
            }
            include $template_path;
        }
    }
}
