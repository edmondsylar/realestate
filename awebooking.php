<?php
global $wp_filter, $wp_actions, $wp_current_filter, $hh_fonts, $post, $old_post, $booking, $old_booking;
function ddd($arr)
{
	echo '<pre style="background: #000; padding: 20px; color: #fff;">';
	print_r($arr);
	echo '</pre>';
}

function hh_load($folder)
{
    $path = dirname(__FILE__);
    $app = $path . '/app/';
    $files = glob($app . $folder . "/*");
    if (!empty($files)) {
        foreach ($files as $key => $file) {
            if (is_file($file)) {
                $filename = hh_path_info($file);
                if (strlen($filename) >= 5) {
                    if (substr($filename, -4) == '.php') {
                        $name = substr($filename, 0, -4);
                        require_once($file);
                        if (class_exists($name)) {
                            $testClass = new ReflectionClass($name);
                            if (!$testClass->isAbstract()) {
                                if (method_exists($name, 'get_inst')) {
                                    $name::get_inst();
                                } elseif (method_exists($name, 'not')) {

                                } else {
                                    new $name();
                                }
                            }
                        }
                    }
                }
            } elseif (is_dir($file)) {
                $dir = $folder . '/' . hh_path_info($file);
                hh_load($dir);
            }
        }
    }
}

function hh_path_info($path = '', $return = '')
{
    if ($return == 'dir') {
        $pathinfo = pathinfo($path);
        $result = $pathinfo['dirname'];
    } else {
        $pathinfo = pathinfo($path);
        $result = $pathinfo['basename'];
    }

    return $result;
}

hh_load('Hooks');
hh_load('Abstracts');
hh_load('Helpers');
hh_load('Libraries');
hh_load('Payments');

if ($wp_filter) {
    $wp_filter = Hook::build_preinitialized_hooks($wp_filter);
} else {
    $wp_filter = array();
}

if (!isset($wp_actions)) {
    $wp_actions = array();
}

if (!isset($wp_current_filter)) {
    $wp_current_filter = array();
}
