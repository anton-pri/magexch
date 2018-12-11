<?php
/* ======================
 * Vendor: cw (cartworks)
 * Module: news
 * ======================
 */
namespace cw\news;

/* --------------------
 * Events
 * --------------------
 */

function on_profile_modify($customer_id, $profile) {

    if (!isset($profile['mailing_list'])) return true;

    $user = \Customer\get($customer_id);
    if (empty($user)) return null;

    global $tables;

    db_query("DELETE FROM $tables[newslist_subscription] WHERE email='$user[email]' AND list_id NOT IN ('".join("','",array_keys($profile['mailing_list']))."')");

    foreach ($profile['mailing_list'] as $lid=>$v) {
		if (!is_numeric($lid) || $lid < 1) continue;
        $is_subscribed = (boolean)cw_query("SELECT list_id FROM $tables[newslist_subscription] WHERE email='$user[email]' AND list_id='$lid'");
        if (!$is_subscribed && $v) {
        $data = array(
            'list_id' => $lid,
            'email' => $user['email'],
            );
        cw_array2insert('newslist_subscription', $data);
        }
    }


}

/* --------------------
 * Methods
 * --------------------
 */

function get_newslists($params=array()) {
    global $tables;

    $where = '';
    foreach ($params as $k=>$v) {
        if (in_array($k,array('list_id','name','show_as_news','avail','subscribe','lngcode','salesman_customer_id',true))) {
            $where .= " AND $k='$v'";
        }
    }
    return cw_query("SELECT * FROM $tables[newslists] WHERE 1 $where");
}

function get_newslist($list_id) {
    $a = get_newslists(array('list_id'=>intval($list_id)));
    return array_pop($a);
}

function get_available_newslists() {
    return get_newslists(array('avail'=>1));
}

function get_available_home_newslists() {
    return get_newslists(array('avail' => 1, 'subscribe' => 1));
}

function get_available_newslists_home_customer() {
    return get_newslists(array('avail'=>1));
}


function get_messages($membership_id, $lngcode, $only_first=false, $limit = null) {
	global $tables, $customer_id;

# kornev
	$query = "
select
	$tables[newsletter].*
from
	$tables[newsletter], $tables[newslists]
left join
    $tables[newslists_memberships] on $tables[newslists].list_id = $tables[newslists_memberships].list_id
where
    ($tables[newslists_memberships].membership_id IS NULL or $tables[newslists_memberships].membership_id = '$membership_id') AND
	$tables[newslists].avail=1 AND
	$tables[newslists].show_as_news=1 AND
	$tables[newslists].lngcode='$lngcode' AND
	$tables[newslists].list_id=$tables[newsletter].list_id AND
	$tables[newsletter].show_as_news=1
order by $tables[newsletter].send_date DESC";
    if ($limit) $query .= " LIMIT $limit";
	elseif ($only_first) $query .= " LIMIT 1";

	$result = cw_query($query);
	if (!is_array($result) || empty($result))
		return false;

	return 	$only_first ? $result[0] : $result;
}

function get_subscribers($list_id, $direct_only = null, $limit = null) {
    global $tables;

    $direct_subscribers = $membership_subscribers = array();

    if (is_null($direct_only) || $direct_only===true) {
        $direct_subscribers = cw_query_hash("select email as hashkey, email, since_date, 1 as direct, '' as membership from $tables[newslist_subscription] WHERE list_id='$list_id' ORDER by email",
    'hashkey', false, false);
    }

    if (is_null($direct_only) || $direct_only === false) {
        $mems = cw_query_column("select membership_id from $tables[newslists_memberships] where list_id='$list_id'");
        if (!count($mems)) $mems = array(-1);
        $membership_subscribers = cw_query_hash("select email as hashkey, email, 0 as since_date, 0 as direct, m.membership from $tables[customers] c LEFT JOIN $tables[memberships] m ON m.membership_id = c.membership_id WHERE c.membership_id IN (".implode(', ', $mems).") ORDER by email",
        'hashkey', false, false);
    }

    $result = array_merge($membership_subscribers,$direct_subscribers);
    ksort($result, SORT_STRING|SORT_FLAG_CASE);
    return $result;
 }

function get_newslists_by_customer($customer_id, $direct_only = null) {
    global $tables;

    $user = \Customer\get($customer_id);
    if (empty($user)) return null;

    $direct = $indirect = array();

    if (is_null($direct_only) || $direct_only===true) {
        $direct = cw_query_column("SELECT n.list_id FROM $tables[newslist_subscription] s
    INNER JOIN $tables[newslists] n ON n.list_id=s.list_id
    WHERE s.email='$user[email]'");
    }

    if (is_null($direct_only) || $direct_only === false) {
        $indirect = cw_query_column("SELECT n.list_id FROM $tables[newslists_memberships] m
    INNER JOIN $tables[newslists] n ON n.list_id=m.list_id
    WHERE m.membership_id='$user[membership_id]'");
    }

    $all = array_merge($indirect,$direct);

    $result = array();
    foreach ($all as $lid) {
        $result[$lid] = array_merge(get_newslist($lid), array('direct'=>intval(in_array($lid,$direct,true)), 'by_membership'=>intval(in_array($lid,$indirect,true))));
    }

    return $result;
}

?>
