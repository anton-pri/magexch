<?php

function cw_image_verification_on_register_validate($register, $usertype) {

    global $config;
    $result = false;
    $page = "on_registration";
    $antibot_err = &cw_session_register("antibot_err");
    global $antibot_validation_val, $antibot_input_str;
    if ($config['image_verification']['spambot_arrest_on_registration'] == "Y") {
        $antibot_err = false;
        if (isset($antibot_input_str) && !empty($antibot_input_str)) {
            $antibot_err = cw_validate_image($antibot_validation_val[$page], $antibot_input_str);
        } else {
            $antibot_err = true;
        }

        if ($antibot_err) {
            $result = array('image_verification' => 'Please enter correct graphic code');   
        } 

    }
    return $result;
}
