<?php
    if($count != 1){
        $home_text = sprintf(__('%s Homes'), $count);
    }else{
        $home_text = sprintf(__('%s Home'), $count);
    }
    $address_text = '';
    if(!empty($address)){
        $address_text = sprintf(__('in %s'), '<strong>'. urldecode(esc_html($address)) .'</strong>');
    }

    $date_string = '';
    if(!empty($check_in) && !empty($check_out)){
        $date_string = sprintf(__('from %s to %s'), '<strong>'. esc_html($check_in) .'</strong>', '<strong>'. esc_html($check_out) .'</strong>');
    }

?>
{{__('Found')}} <span class="item-found"><?php echo balanceTags($home_text); ?></span> <?php echo balanceTags($address_text . ' ' . $date_string) ?>
