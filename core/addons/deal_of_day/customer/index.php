<?php

$dod_offer_id = cw_query_first_cell("select pso.offer_id from $tables[ps_offers] pso inner join $tables[dod_generators] dodg on pso.offer_id=dodg.current_offer_id and dodg.active=1 and dodg.startdate<'".time()."' and dodg.enddate>'".time()."' where pso.active=1 and pso.startdate<'".time()."' and pso.enddate>'".time()."'");

if ($dod_offer_id) { 
    $dod_product_id = cw_query_first_cell("select pscd.object_id from $tables[ps_cond_details] pscd inner join $tables[ps_conditions] psc on pscd.cond_id=psc.cond_id and pscd.offer_id=psc.offer_id and psc.type='P' where pscd.object_type='1' and pscd.offer_id='$dod_offer_id'");
    if ($dod_product_id) {
        global $user_account;   
        $deal_of_day_product = cw_func_call('cw_product_get' ,array('id' => $dod_product_id, 'user_account' => $user_account, 'info_type' => 65535)); 
        $smarty->assign('deal_of_day_product', $deal_of_day_product); 
    }
    $smarty->assign('dod_offer', cw_query_first("select * from $tables[ps_offers] where offer_id='$dod_offer_id'"));
}

$smarty->assign('home_offer', 'Y');
