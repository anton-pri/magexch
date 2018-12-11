<?php
set_time_limit(86400);
global $customer_id, $user, $userinfo;
global $current_area;

/*
 * Definition of main variables
 */

// The only admin can work with other users
// In customer area $user and $customer_id must be equal
if ($current_area != 'A') {
    $user = $customer_id;
}

$smarty->assign('user', $user);

/*
 *  Execute appropriate controller
 */
if ($mode == 'add') {
    $userinfo['usertype'] = $usertype;
    $userinfo['status'] = 'Y';
    cw_include('include/users/modify.php');

    $location[] = array(cw_get_langvar_by_name('lbl_users_'.$usertype), 'index.php?target='.$target);
    $location[] = array(cw_get_langvar_by_name('lbl_user_type_'.$usertype), '');
    $smarty->assign('main', 'modify');
}
elseif ($user && ($action == 'suspend_account')) {
    cw_include('include/users/suspend_account.php');
}
elseif ($user && ($mode == 'modify' || $action == 'update')) {
    cw_include('include/users/modify.php');

    $location[] = array(cw_get_langvar_by_name('lbl_users_'.$usertype), 'index.php?target='.$target);
    $location[] = array(cw_get_langvar_by_name('lbl_user_type_'.$usertype), '');
    $smarty->assign('main', 'modify');
}
elseif ($mode == 'process' || $mode == 'delete')
    cw_include('include/users/process.php');
elseif ($mode == 'contracts')
    cw_include('include/users/contracts.php');
elseif ($mode == 'activities')
    cw_include('include/users/activities.php');
elseif ($mode == 'addresses')
    cw_include('include/users/addresses.php');
elseif ($mode == 'purchased_products')
    cw_include('include/users/purchased_products.php');
elseif ($mode == 'docs')
    cw_include('include/users/docs.php');
elseif ($mode == 'photos')
    cw_include('include/users/photos.php');
else {
    cw_include('include/users/search.php');
    $location[] = array(cw_get_langvar_by_name('lbl_users_'.$usertype), 'index.php?target='.$target);
    $smarty->assign('usertype_search', $usertype);
    $smarty->assign('main', 'search');
}
$smarty->assign('mode', $mode);
