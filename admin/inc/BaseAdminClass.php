<?php

class BaseAdminClass
{
    protected function loadTemplate($template_name, $args = array())
    {
        if (empty($template_name) || !is_string($template_name)) {
            error_log('Invalid template name provided to loadTemplate.');
            return;
        }
    
        $template_path = plugin_dir_path(__FILE__) . '../../admin/templates/' . $template_name . '.php';
    
        if (file_exists($template_path)) {
            if (!empty($args) && is_array($args)) {
                extract($args, EXTR_SKIP); 
            } elseif (!empty($args)) {
                error_log('The arguments provided to loadTemplate must be an array. Given: ' . print_r($args, true));
            }
            include $template_path;
        } else {
            error_log('Template file not found: ' . $template_path);
        }
    }
    
}
