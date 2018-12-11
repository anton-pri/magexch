<?php
function dashboard_build_sections($params, $return=null) {
    return $return;
}

#
# Function returns dates by specific range title:
#  month, week, beweek, range
# Input: string range
# Return: array(<from_date>,<to_date>)
    function cw_get_dates_by_range($range) {

			$date = getdate();
			switch  ($range) {
            case 'today':
				$from = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
				$to = time();
            break;
            case 'year':
				$from = mktime(0,0,0,1,1,$date['year']);
				$to = time();
			break;
            case 'month':
				$from = mktime(0,0,0,$date['mon'],1,$date['year']);
				$to = time();
			break;
            case '30days':
                $to = time();
                $from = $to - 60*60*24*30;
            break;
            case '365days':
                $to = time();
                $from = $to - 60*60*24*365;
            break;
			case 'week':
				$week_day = date('N')-1; # 0 - Monday
				$week_sec = 60*60*24*$week_day;
				$cur_time = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
				$from_time = $cur_time - $week_sec;
				$from = $from_time;
				$to = time();
			break;
			case 'beweek':
				if ($date['mday']<15) $day = 1; else $day = 15;
				$from = mktime(0,0,0,$date['mon'],$day,$date['year']);
				$to = time();
			break;
			case 'prev_beweek':
				if ($date['mday']<15) {
                                    $day = 15;
                                    $date['mon']--;
                                } else {
                                    $day = 1;
                                }
				$from = mktime(0,0,0,$date['mon'],$day,$date['year']);
				if ($day==1) {
                                    $day = 15;
                                } else {
                                    $day = 1;
                                    $date['mon']++; // Thanks to PHP it understands what does 13th month mean as well as 0th
                                }
				$to = mktime(0,0,0,$date['mon'],$day,$date['year'])-1;
			break;
            /* Login history tracking is disabled
            case 'last_login':
                global $customer_id, $tables;
                $from = cw_query_first_cell("SELECT date FROM $tables[customers_login_history]
                        WHERE customer_id=$customer_id AND action='login' AND status='success'
                        ORDER BY date DESC LIMIT 1,1");
            break;
             */
			}

		return array($from,$to);

	}
#
# Function returns the list of the currently 
# defined sections in the cw_dashboard table
#
function dashboard_get_sections_list() {
    global $tables;

    $result = cw_query_hash("SELECT * FROM $tables[dashboard]", 'name', false, false);

    return $result;
}

#
# Function returns the dashboard array which is used to 
# build the dashboard sections on the main page
#
function dashboard_display_prepare() { 
    global $tables;

    $params = array(
        'mode' => 'dashboard',
        'sections' => cw_call('dashboard_get_sections_list')
    );

    $dashboard = cw_func_call('dashboard_build_sections',$params);

    // Re-check if some addon ignored active flag
    foreach ($dashboard as $name=>$dash) {

        $dashboard[$name] = array_merge(array('frame'=>1, 'header'=>1),$dashboard[$name]);

        if (isset($params['sections'][$name])) {
            $dashboard[$name] = array_merge($dashboard[$name],$params['sections'][$name]);
        }

        if ($dashboard[$name]['active']==0) unset($dashboard[$name]);
    }

    uasort($dashboard, 'cw_uasort_by_order');

    return $dashboard;
}
