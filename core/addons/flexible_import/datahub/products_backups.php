<?php

if ($action == 'new') {

    $new_backup = cw_call('cw_datahub_product_backup_create', array()); 
    if ($new_backup['backup_id']) {
        if ($new_backup['is_new']) {
            cw_add_top_message('New products backup saved successfully', 'I');
        } else {
            $date_existing_backup = date('Y-m-d__H-i-s', $new_backup['backup']['date']);
            cw_add_top_message("The products backup file already has been saved earlier on $date_existing_backup, skipped saving to avoid files duplicate", 'I');  
        }  
    } else
        cw_add_top_message('Could not write new products backup', 'E');

    cw_header_location('index.php?target=datahub_products_backups');    
} elseif ($action == 'restore') {
    cw_call('cw_datahub_product_backup_save_current_and_restore', array($restore_backup_id));

    cw_add_top_message('Selected backup has been restored successfully', 'I');

    cw_header_location('index.php?target=datahub_products_backups');
}


$products_backups = cw_call('cw_datahub_products_backups_list', array());
if (!empty($products_backups)) 
    $smarty->assign('datahub_products_backups', $products_backups);

$smarty->assign('main', 'datahub_products_backups');
