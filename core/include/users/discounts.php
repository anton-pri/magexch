<?php
if ($action == 'update_discount' && is_array($update_discount)) {
    foreach($update_discount as $id=>$val) {
        if ($val['discount'] > 100) $val['discount'] = 100;
        if (empty($id)) {
            $val['customer_id'] = $user;
            if ($val['discount'])
                cw_array2insert('customers_discounts', $val, false, array('discount', 'orderby', 'customer_id'));
        }         
        else
            cw_array2update('customers_discounts', $val, "discount_id='$id'", array('discount', 'orderby'));
    }
    cw_header_location("index.php?target=$target&mode=$mode&user=$user");
}

if ($action == 'delete_discount' && is_array($del)) {
    foreach($del as $id=>$val)
        db_query("delete from $tables[customers_discounts] where discount_id='$id' and customer_id='$user'");
    cw_header_location("index.php?target=$target&mode=$mode&user=$user");
}

$smarty->assign('discounts', cw_query("select * from $tables[customers_discounts] where customer_id='$user' order by orderby"));
$smarty->assign('user', $user);

$smarty->assign('current_section', '');
$smarty->assign('home_style', 'iframe');
$smarty->assign('main', 'discounts');
?>
