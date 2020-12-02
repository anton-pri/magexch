<?php
/* =================================
 * Customer
 *
 * =================================
 */
namespace Customer;

define('CUSTOMER_STATIC_CACHE',2); // Usually we need cache for two users max - admin and customer

function get($id) {
    global $tables;
    static $cache;
    if (is_null($id)) { // Trick to cleanup cache
        $cache = array();
        return null;
    }
    if (!isset($cache[$id])) {
        $data = cw_query_first('SELECT * FROM '.$tables['customers'].' WHERE customer_id="'.intval($id).'"');
        $cache[$id] = $data;
        if (count($cache)>CUSTOMER_STATIC_CACHE) {
            reset($cache); // sets internal array pointer to start
            unset($cache[key($cache)]); // key() returns key of current array element
        }
    }

    return $cache[$id];
}

function getByEmail($email) {
    global $tables;
    return cw_query_first('SELECT * FROM '.$tables['customers'].' WHERE email="'.db_escape_string($email).'" ORDER BY customer_id ASC');
}

function getByEmailCustomer($email) {
    global $tables, $current_area;

    if ($current_area == 'C') {
        $result = cw_query_first(
            "SELECT c.* 
            FROM $tables[customers] c 
            INNER JOIN $tables[customers_system_info] csi 
            ON csi.customer_id=c.customer_id AND csi.last_login!=0 
            WHERE c.email='" . db_escape_string($email) . "' 
            ORDER BY c.customer_id ASC");

        return $result;    
    } else
        return getByEmail($email);    
}


function getField($id,$field) {
    $data = get($id);
    return $data[$field];
}

function isActive($id) {
    return getField($id,'status') == 'Y';
}

function getUsertype($id) {
    return getField($id,'usertype');
}

function getEmail($id) {
    return getField($id,'email');
}

function delete($id) {
    global $tables;
    db_query('DELETE FROM '.$tables['customers'].' WHERE customer_id="'.intval($id).'"');
    cw_event('on_customer_delete',array($id));
    get(null);
}


/* =================================
 * Customer\Address
 *
 * =================================
 */
namespace Customer\Address;

/* ---------------------------------
 * Events handlers
 * ---------------------------------
 */

cw_event_listen('on_customer_delete','\Customer\Address\on_customer_delete');

function on_customer_delete($customer_id) {
    \Customer\Address\delete(null,$customer_id);
}


/* ---------------------------------
 * Interface
 * ---------------------------------
 */

function delete($address_id = null, $customer_id = null) {
    global $tables;
    $where = (!is_null($address_id)?' AND address_id = "'.intval($address_id).'"':'').(!is_null($customer_id)?' AND customer_id = "'.intval($customer_id).'"':'');
    return db_query('DELETE FROM '.$tables['customers_addresses'].' WHERE 1 '.$where);
}

// null|string $type = [main,current]
function get($address_id = null, $customer_id = null, $type = null) {
    global $tables;
    if (!empty($type) && !in_array($type,array('main','current'))) return false;

    $where = (!is_null($address_id)?' AND address_id = "'.intval($address_id).'"':'').(!is_null($customer_id)?' AND customer_id = "'.intval($customer_id).'"':'').(!is_null($type)?" AND $type = 1":"");
    $result = cw_query('SELECT * FROM '.$tables['customers_addresses'].' WHERE 1 '.$where.' ORDER BY address_id');

    // if certain address requested, then return only this row
    if (!is_null($address_id) || (!is_null($customer_id) && !is_null($type)) && count($result)==1) return array_pop($result);
    // otherwise return array of rows
    return $result;
}

// Set certain or first available address as main or current
// string type = [main,current]
function setAddressType($customer_id, $type, $address_id = null) {

    if (empty($customer_id)) return false;
    if (!in_array($type,array('main','current'))) return false;

    $data = array($type=>0);
    cw_array2update('customers_addresses',$data,'customer_id = "'.intval($customer_id).'"');

    $data = array($type=>1);
    cw_array2update('customers_addresses',$data,'customer_id = "'.intval($customer_id).'"'.(!is_null($address_id)?' AND address_id = "'.intval($address_id).'"':'').' LIMIT 1');
}

// Get main (billing) address
function getMain($customer_id) {
    return get(null, $customer_id, 'main');
}

// Get current (shipping) address
function getCurrent($customer_id) {
    return get(null, $customer_id, 'current');
}
