<?php
cw_load('doc', 'web', 'attributes');
$orders = array ();

if (!empty($doc_ids)) {

        $_doc_ids = explode(',', $doc_ids);
            
        if (!$customer_id && empty($request_prepared['skey'])) {
            cw_header_location("index.php?target=error_message&error=access_denied&id=32");
        }
        
        if (isset($request_prepared['skey'])) {
            $seed = strpos($request_prepared['skey'],'s-')===0?$APP_SESS_ID:null;
            if ($request_prepared['skey']!=cw_call('cw_doc_security_key', array($_doc_ids,$seed))) {
                cw_header_location("index.php?target=error_message&error=access_denied&id=322");                
            }
        }
        
        $layout = '';
        foreach ($_doc_ids as $doc_id) {
            $doc_data = cw_call('cw_doc_get', array($doc_id, 1));
            if (empty($doc_data) || 
               (
                    !isset($request_prepared['skey']) && 
                    $doc_data['userinfo']['customer_id'] != $customer_id
               )
            ) {
                unset($doc_data);
                continue;
            }
            $orders[] = $doc_data;
            if (!$layout) {
                $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type'], ($_GET['is_email_invoice'] == 'Y')));
                $smarty->assign('layout_data', $layout);
            }
        }
    }

if (empty($orders))
    cw_header_location('index.php?target=error_message&error=access_denied&id=59');

$location[] = array(cw_get_langvar_by_name('lbl_doc_info_O'), '');

$smarty->assign('product_layout_elements',cw_call('cw_web_get_product_layout_elements', array()));
$smarty->assign('orders', $orders);
$smarty->assign('doc_ids', $doc_ids);

$smarty->assign('current_section_dir', 'cart');
$smarty->assign('main', 'order_message');

$smarty->assign('is_email_invoice', 'Y');

if ($_GET['standalone_mode'] == 'Y') {
 
    if (is_array($doc_ids)) 
        $doc_id = $doc_ids[0];
    elseif (is_numeric($doc_ids))
        $doc_id = $doc_ids;    

    $doc_data = cw_call('cw_doc_get', array($doc_id));

    if ($doc_data['info']['layout_id']) 
        $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
    else
        $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type'], ($_GET['is_email_invoice'] == 'Y')));

    $smarty->assign('layout_data', $layout);
    $smarty->assign('info', $doc_data['info']);
    $smarty->assign('products', $doc_data['products']);
    $smarty->assign('doc', $doc_data);

    if ($_GET['is_email_invoice'] == 'Y') {
        $smarty->assign('is_email_invoice', $_GET['is_email_invoice']);
        cw_display('mail/docs/customer.tpl', $smarty);
    } else {
        cw_display('customer/cart/order_message.tpl', $smarty);
    }
    exit;
}
