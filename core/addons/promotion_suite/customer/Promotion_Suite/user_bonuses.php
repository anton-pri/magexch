<?php
// CartWorks.com - Promotion Suite 
if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons['Promotion_Suite'])) return false;

/*
Script updates $user_bonuses array with all active bonuses applicable for current user taking into account his zone.
Depending on PS_FORCE_USER_BONUSES may use session or extract it every time.
*/

// bonuses were already once extracted earlier in this script (not in session)
if (!empty($user_bonuses)) return true;

// $user_bonuses is empty, try to find it in session
x_session_register('user_bonuses');
x_session_register('user_bonuses_logged_userid');

// Do not update bonuses for current user if
// - it is already stored in session
// - AND stored for current logged_userid
// - AND update is not forced by config
if (!empty($user_bonuses) && ($logged_userid==$user_bonuses_logged_userid) && !PS_FORCE_USER_BONUSES) return true;

x_load('cart');

if (empty($logged_userid) && $config["General"]["apply_default_country"] == "Y") {
                # Use the default address
                $userinfo["b_country"] = $userinfo["s_country"] = $config["General"]["default_country"];
                $userinfo["b_state"] = $userinfo["s_state"] = $config["General"]["default_state"];
                $userinfo["b_zipcode"] = $userinfo["s_zipcode"] = $config["General"]["default_zipcode"];
                $userinfo["b_city"] = $userinfo["s_city"] = $config["General"]["default_city"];
                $userinfo["b_countryname"] = $userinfo["s_countryname"] = cw_get_country($userinfo["s_country"]);
                $userinfo["b_statename"] = $userinfo["s_statename"] = cw_get_state($userinfo["s_state"], $userinfo["s_country"]);
}

if (!empty($logged_userid))
        $userinfo = cw_userinfo($logged_userid, $current_area);

        $where_statement = ''; $join_statement='';

# Multidomain addon integration
        if (!empty($domain_info)) {
                        $join_statement = " LEFT JOIN $tables[domain_bonuses] ON $tables[domain_bonuses].bonusid = b.bonusid AND $tables[domain_bonuses].domainid = $domain_info[domainid]";
                        $where_statement = " AND  $tables[domain_bonuses].bonusid IS NOT NULL";
                }
# / Multidomain addon integration

$user_bonuses = cw_query_hash("SELECT b.bonusid, b.*, IF(bl.bonus_name IS NOT NULL,bl.bonus_name,b.bonus_name) as bonus_name, bl.bonus_desc as bonus_desc, IF(bi.id IS NULL,'','Y') as is_image FROM $tables[bonuses] b LEFT JOIN $tables[images_PS] bi ON bi.id=b.bonusid LEFT JOIN $tables[bonuses_lng] bl ON b.bonusid=bl.bonusid AND bl.code='$shop_language' $join_statement WHERE b.bonus_active='Y' and b.start_date<UNIX_TIMESTAMP() and b.end_date>UNIX_TIMESTAMP() AND b.pid=0 $where_statement ORDER BY ".(empty($cat)?'pos':'priority'),'bonusid',0,0);


if (!empty($user_bonuses)) {
    foreach ($user_bonuses as $_bid=>$v) {
		$user_bonuses[$_bid]['bonusid'] = $_bid;
        if (!empty($logged_userid) || $config["General"]["apply_default_country"] == "Y") {
            if (!cw_check_condition_Z($_bid)) {
                unset($user_bonuses[$_bid]); continue;
            }
        }
    }
}

$user_bonuses_logged_userid = $logged_userid;

x_session_save('user_bonuses','user_bonuses_logged_userid');

// CartWorks.com - Promotion Suite 
?>
