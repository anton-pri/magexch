<?php
namespace cw\product_stages;

function on_cron_daily() {
    //send out here periodical stage emails according to purchased products having stages
    cw_call('cw\\'.addon_name.'\\cw_product_stages_send_emails');
}

function getWorkingDays($startDate,$endDate,$holidays){

    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

function cw_product_stages_days_passed($date_past, $since_date = 0) {
    if (!$since_date)
        $since_date = time();

    $startDate = min($date_past, $since_date);
    $endDate = max($date_past, $since_date);

    return getWorkingDays($startDate, $endDate, array());
    //return floor((time()-$date_past)/(24*60*60));
}

function cw_product_stages_date_in_past($period, $since_date = 0) {
    if (!$since_date)
        $since_date = time();

    $safety_cnt = 1000;
    $endDate = $since_date;
    $bd_period = 0;
    while ($period > $bd_period && $safety_cnt > 0) {
        $bd_period = getWorkingDays($endDate,$since_date,array());
        $endDate -= 24*60*60;
        $safety_cnt--;
    }

    return $endDate;
//  return (time() - $period*24*60*60);
}

function cw_product_stages_date_in_future($period, $since_date = 0) {
    if (!$since_date) 
        $since_date = time();

    $safety_cnt = 1000;
    $endDate = $since_date;
    $bd_period = 0; 
    while ($period > $bd_period && $safety_cnt > 0) {
        $bd_period = getWorkingDays($since_date,$endDate,array());           
        $endDate += 24*60*60;
        $safety_cnt--;
    }

    return $endDate;
//  return ($since_date + $period*24*60*60);
}

function cw_product_stages_send_emails() {
    global $tables, $smarty, $config;
    cw_load('doc');
    //select all active product stages settings 
    $stages_settings = cw_query("select ps.title, ps.default_status, ps.default_period, ps.subject, ps.body, psp.* from $tables[product_stages_product_settings] psp inner join $tables[product_stages_library] ps on ps.stage_lib_id=psp.stage_lib_id where psp.active=1");

    foreach ($stages_settings as $stage_setting) {

        $stage_statuses = array();

        if (!empty($stage_setting['status']) && $stage_setting['status'] != -1)
            $stage_statuses = unserialize($stage_setting['status']);
        elseif ($stage_setting['status'] == -1 && !empty($stage_setting['default_status'])) 
            $stage_statuses = unserialize($stage_setting['default_status']);

        if (empty($stage_statuses)) continue;

        if ($stage_setting['period'] != -1) 
            $stage_period = $stage_setting['period'];
        else
            $stage_period = $stage_setting['default_period']; 

        $smarty->assign('stage_message_subject', $stage_setting['subject']);
        $smarty->assign('stage_message_body', $stage_setting['body']);

        //find all ordered products which have been bought within stage period and which are linked to current stage_setting
        $date_past = cw_product_stages_date_in_past($stage_period);
        $ordered_items = cw_query($s = "select di.* from $tables[docs_items] di inner join $tables[docs] d on d.doc_id=di.doc_id and d.date >= '$date_past' where di.product_id = '$stage_setting[product_id]'");
//print("$s <br/>\n");
        foreach ($ordered_items as $doc_item) {

            $doc_data = array();

            //get status change history
            $status_log = cw_query("select * from $tables[docs_statuses_log] where doc_id='$doc_item[doc_id]' and status in ('".implode("','", $stage_statuses)."')");  
            foreach ($status_log as $st_log) { 
                $days_passed = cw_product_stages_days_passed($st_log['date']);  
                if ($days_passed >= $stage_period) {
                    $stage_is_processed = cw_query_first_cell("select count(*) from $tables[product_stages_process_log] where setting_id='$stage_setting[setting_id]' and doc_item_id='$doc_item[item_id]' and status='$st_log[status]'");
                    if (!$stage_is_processed) {

                        $smarty->assign('stage_status', $st_log['status']);
                        $smarty->assign('stage_status_date', $st_log['date']);
 
                        if (empty($doc_data)) {
                            $doc_data = cw_doc_get($doc_item['doc_id'], 8192);
                            $smarty->assign('info', $doc_data['info']);
                            $smarty->assign('products', $doc_data['products']);
                            $smarty->assign('order', $doc_data);
                            $smarty->assign('doc', $doc_data);
                            $smarty->assign('userinfo', $doc_data['userinfo']);

cw_log_add('stage_process', array($st_log, $stage_setting, $doc_data));
                        }  

                        cw_call('cw_send_mail', 
                            array(
                                $config['Company']['orders_department'], 
                                $doc_data['userinfo']['email'], 
                                'addons/product_stages/mail/stage_notification_subj.tpl', 
                                'addons/product_stages/mail/stage_notification.tpl'
                            )
                        );
                        cw_array2insert('product_stages_process_log', 
                            array('setting_id'=> $stage_setting['setting_id'], 
                                  'doc_item_id'=> $doc_item['item_id'], 
                                  'status'=> $st_log['status'], 
                                  'date'=> time()
                            )
                        );
                    }    
                } 
            }
        }
    }
}


function cw_product_stages_on_doc_change_status($doc_data, $new_status) {

    cw_array2insert('docs_statuses_log', array('doc_id'=>$doc_data['doc_id'], 'status'=>$new_status, 'date'=>time()));
}



function cw_product_stages_tabs_js_abstract($params, $return) {
    if ($return['name'] == 'product_data') {
        if (AREA_TYPE != 'A') return $return;

        $return['js_tabs']['product_stages'] = array(
            'title' => cw_get_langvar_by_name('lbl_order_stages'),
            'template' => 'addons/product_stages/main/products/product/stages.tpl',
        );
    } elseif ($return['name'] == 'doc_O_info') {
        if (AREA_TYPE != 'A') return $return;

        $return['js_tabs']['order_stages'] = array(
            'title' => cw_get_langvar_by_name('lbl_order_stages'),
            'template' => 'addons/product_stages/doc_O_stages.tpl',
        );
    }

    return $return;
}



function cw_product_stages_get_product_settings($product_id, $extra_condition = '') {
    global $tables;
    $product_stages = cw_query("select ps.title, ps.default_status, ps.default_period, ps.subject, ps.body, psp.* from $tables[product_stages_product_settings] psp inner join $tables[product_stages_library] ps on ps.stage_lib_id=psp.stage_lib_id where psp.product_id='$product_id' $extra_condition");  

    foreach ($product_stages as $ps_k => $ps_v) {
        if (!empty($ps_v['status']) && $ps_v['status'] != -1)  
            $product_stages[$ps_k]['status'] = unserialize($ps_v['status']);

        if (!empty($ps_v['default_status'])) 
            $product_stages[$ps_k]['default_status'] = unserialize($ps_v['default_status']);
    }

    return $product_stages;
}

function cw_product_stages_get_doc_stages_history($products) {
    global $tables;
    $result = array();
    foreach ($products as $doc_item) {
        $processed_stages = cw_query("select ps.title, ps.default_status, ps.default_period, ps.subject, ps.body, psp.status, psp.period, psl.setting_id, psl.doc_item_id, psl.status as log_status, psl.date from $tables[product_stages_process_log] psl inner join $tables[product_stages_product_settings] psp on psp.setting_id = psl.setting_id inner join $tables[product_stages_library] ps on ps.stage_lib_id=psp.stage_lib_id where psl.doc_item_id='$doc_item[item_id]' order by psl.date");

        $processed_stages_ids = array();

        if (!empty($processed_stages)) {

            foreach ($processed_stages as $s_k => $proc_stage) {

                if (!empty($proc_stage['status']) && $proc_stage['status'] != -1)
                    $processed_stages[$s_k]['stage_statuses'] = unserialize($proc_stage['status']);
                elseif ($proc_stage['status'] == -1 && !empty($proc_stage['default_status']))
                    $processed_stages[$s_k]['stage_statuses'] = unserialize($proc_stage['default_status']);

                if ($proc_stage['period'] != -1)
                    $processed_stages[$s_k]['stage_period'] = $proc_stage['period'];
                else
                    $processed_stages[$s_k]['stage_period'] = $proc_stage['default_period'];


                $processed_stages_ids[] = $proc_stage['setting_id'];
            }

            $result[$doc_item['item_id']]['processed_stages'] = $processed_stages;
        } 

        $expected_stages = cw_query("select ps.title, ps.default_status, ps.default_period, ps.subject, ps.body, psp.* from $tables[product_stages_product_settings] psp inner join $tables[product_stages_library] ps on ps.stage_lib_id=psp.stage_lib_id where psp.product_id='$doc_item[product_id]' and psp.active=1 and psp.setting_id not in ('".implode("','", $processed_stages_ids)."')");  

        if (!empty($expected_stages)) { 
            foreach ($expected_stages as $s_k => $exp_stage) {

                if (!empty($exp_stage['status']) && $exp_stage['status'] != -1)
                    $expected_stages[$s_k]['stage_statuses'] = unserialize($exp_stage['status']);
                elseif ($exp_stage['status'] == -1 && !empty($exp_stage['default_status']))
                    $expected_stages[$s_k]['stage_statuses'] = unserialize($exp_stage['default_status']);

                if ($exp_stage['period'] != -1)
                    $expected_stages[$s_k]['stage_period'] = $exp_stage['period'];
                else
                    $expected_stages[$s_k]['stage_period'] = $exp_stage['default_period'];


                //when to trigger
                if (!empty($expected_stages[$s_k]['stage_statuses'])) { 
                    $triggering_status = cw_query_first("select * from $tables[docs_statuses_log] where doc_id='$doc_item[doc_id]' and status in ('".implode("','", $expected_stages[$s_k]['stage_statuses'])."') order by date asc limit 1");
                    if (!empty($triggering_status)) {                    
                        $triggering_status['date_due'] = cw_product_stages_date_in_future($expected_stages[$s_k]['stage_period'], $triggering_status['date']); 
                        $expected_stages[$s_k]['triggering_status'] = $triggering_status;
                    }   
                }
           
            } 
            $result[$doc_item['item_id']]['expected_stages'] = $expected_stages;
        }

    }  
    return $result;
}
