<?php
if (defined('IS_AJAX')) {

    cw_load('user');

    if (isset($_GET['user_id'])) {
        $id = intval($_GET['user_id']);
        $result = cw_user_get_info($id, 1);
        if ($result) {
            $user = array_shift(array_values($result['addresses']));
            $user['address'] = ($user['address']!=''? $user['address'] : $user['address_2']);
            $user['email'] = $result['email'];
            echo json_encode($user);
        }
    }
    exit();
}
