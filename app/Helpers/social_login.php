<?php

function social_enable($type = 'facebook')
{
    $enable = get_option($type . '_login', 'off');
    return $enable == 'on' ? true : false;
}
