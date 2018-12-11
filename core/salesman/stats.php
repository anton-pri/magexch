<?php
$location[] = array(cw_get_langvar_by_name("lbl_summary_statistics"), "");

    $stats_info = array ();

    $result = cw_query_first_cell("SELECT COUNT(*) FROM $tables[docs], $tables[salesman_payment] WHERE $tables[docs].doc_id=$tables[salesman_payment].doc_id AND $tables[salesman_payment].salesman_customer_id='$customer_id'");
    $stats_info ['total_sales'] = $result;

    $result = cw_query_first_cell("SELECT COUNT(*) FROM $tables[docs], $tables[salesman_payment] WHERE $tables[docs].doc_id=$tables[salesman_payment].doc_id AND $tables[salesman_payment].salesman_customer_id='$customer_id' AND $tables[docs].status NOT IN ('C','P')");
    $stats_info ['unapproved_sales'] = $result;

    $result = cw_query_first("SELECT SUM($tables[salesman_payment].commissions) AS numba FROM $tables[salesman_payment], $tables[docs] WHERE $tables[salesman_payment].doc_id=$tables[docs].doc_id AND $tables[salesman_payment].salesman_customer_id='$customer_id' AND $tables[docs].status NOT IN ('C','P') AND $tables[salesman_payment].paid!='Y'");
    $stats_info ['pending_commissions'] = ($result['numba'] ? $result['numba'] : "0.00");

    $result = cw_query_first("SELECT SUM($tables[salesman_payment].commissions) AS numba FROM $tables[salesman_payment], $tables[docs] WHERE $tables[salesman_payment].doc_id=$tables[docs].doc_id AND $tables[salesman_payment].salesman_customer_id='$customer_id' AND $tables[docs].status IN ('P','C') AND $tables[salesman_payment].paid!='Y'");
    $stats_info ['approved_commissions'] = ($result['numba'] ? $result['numba'] : "0.00");

    $result = cw_query_first("SELECT SUM(commissions) AS numba FROM $tables[salesman_payment] WHERE salesman_customer_id='$customer_id' AND paid='Y'");
    $stats_info ['paid_commissions'] = ($result['numba'] ? $result['numba'] : "0.00");

    $smarty->assign ('stats_info', $stats_info);


$smarty->assign('main', 'stats');
