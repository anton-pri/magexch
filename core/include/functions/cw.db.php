<?php
function db_connect($sql_host, $sql_user, $sql_password) {
    global $__mysql_connection_id;
	return $__mysql_connection_id = mysqli_connect('p:'.$sql_host, $sql_user, $sql_password);
}

function db_select_db($sql_db, $mysql_connection_id = null) {
	global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;
	return mysqli_select_db($mysql_connection_id, $sql_db);
}

function db_query($query, $mysql_connection_id = null) {
    global $__mysql_connection_id, $app_config_file;
    global $__sql_counter;

    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

	$start = microtime(true);
	$id = cw_bench_open_tag('', 'sql', $query);

	$result = mysqli_query($mysql_connection_id, $query);

	cw_bench_close_tag($id);

 	if ($app_config_file['debug']['development_mode'] && !defined('IS_AJAX')) {
		$ll = microtime(true) - $start;

		if ($ll > 1 && function_exists('cw_log_add')) {
		   cw_log_add("log_slow_queries", "$ll msec: ".$query, true, 0, 0, 0);
		}

	}

	# Auto repair
	if (!$result && MYSQL_AUTOREPAIR && preg_match("/'(\S+)\.(MYI|MYD)/",mysqli_error($mysql_connection_id), $m)) {
		$stm = "REPAIR TABLE $m[1] EXTENDED";
		error_log("Repairing table $m[1]", 0);
		if (DEBUG_MODE == 1 || DEBUG_MODE == 3) {
			$mysql_error = mysqli_errno($mysql_connection_id)." : ".mysqli_error($mysql_connection_id);
			echo "<b><font COLOR=DARKRED>Repairing table $m[1]...</font></b>$mysql_error<br />";
			flush();
		}

		$result = mysqli_real_query($stm, $mysql_connection_id);
		if (!$result)
			error_log("Repaire table $m[1] is failed: ".mysqli_errno($mysql_connection_id)." : ".mysqli_error($mysql_connection_id), 0);
		else
			$result = mysqli_query($mysql_connection_id, $query); # try repeat query...
	}

	if (db_error($result, $query, $mysql_connection_id) && DEBUG_MODE==1)
		exit;

	return $result;
}


function db_fetch_row($result) {
	return mysqli_fetch_row($result);
}

function db_fetch_array($result, $flag=MYSQLI_ASSOC) {
	return mysqli_fetch_array($result, $flag);
}

function db_fetch_field($result) {
	return mysqli_fetch_field($result);
}

function db_free_result($result) {
	@mysqli_free_result($result);
}

function db_num_rows($result) {
	return mysqli_num_rows($result);
}

function db_num_fields($mysql_connection_id=null) {
	global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;
	return mysqli_field_count($mysql_connection_id);
}

function db_insert_id($mysql_connection_id = null) {
    global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

	return mysqli_insert_id($mysql_connection_id);
}

function db_affected_rows($mysql_connection_id = null) {
    global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

	return mysqli_affected_rows($mysql_connection_id);
}

function db_error($mysql_result, $query, $mysql_connection_id = null) {
	global $config, $customer_id, $REMOTE_ADDR, $current_location;
    global $__mysql_connection_id;

    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

	if ($mysql_result)
		return false;

	$mysql_error = mysqli_errno($mysql_connection_id)." : ".mysqli_error($mysql_connection_id);
	$msg  = "Site        : ".$current_location."\n";
	$msg .= "Remote IP   : $REMOTE_ADDR\n";
	$msg .= "Logged as   : $customer_id\n";
	$msg .= "SQL query   : $query\n";
	$msg .= "Error code  : ".mysqli_errno($mysql_connection_id)."\n";
	$msg .= "Description : ".mysqli_error($mysql_connection_id);

	db_error_generic($query, $mysql_error, $msg);

	return true;
}

function db_error_generic($query, $query_error, $msg) {
	global $config;

	$email = false;

	if (@$config['Email']['admin_sqlerror_notify']=="Y") {
		$email = array ($config['Company']['site_administrator']);
	}

	if (DEBUG_MODE == 1 || DEBUG_MODE == 3) {
		echo '<div>INVALID SQL:'.htmlspecialchars($query_error)."</div>\n";
		echo '<div>SQL QUERY FAILURE:'.htmlspecialchars($query)."</div>\n";
        print_r(cw_get_backtrace());
		flush();
	}

	$do_log = (DEBUG_MODE == 2 || DEBUG_MODE == 3);

	if ($email !== false || $do_log)
		cw_log_add('SQL', $msg, true, 1, $email, !$do_log);
}

function db_prepare_query($query, $params) {
	static $prepared = array();

	if (!empty($prepared[$query])) {
		$info = $prepared[$query];
		$tokens = $info['tokens'];
	}
	else {
		$tokens = preg_split('/((?<!\\\)\?)/S', $query, -1, PREG_SPLIT_DELIM_CAPTURE);

		$count = 0;
		foreach ($tokens as $k=>$v) if ($v === '?') $count ++;

		$info = array (
			'tokens' => $tokens,
			'param_count' => $count
		);
		$prepared[$query] = $info;
	}

	if (count($params) != $info['param_count']) {
		return array (
			'info' => 'mismatch',
			'expected' => $info['param_count'],
			'actual' => count($params));
	}

	$pos = 0;
	foreach ($tokens as $k=>$val) {
		if ($val !== '?') continue;

		if (!isset($params[$pos])) {
			return array (
				'info' => 'missing',
				'param' => $pos,
				'expected' => $info['param_count'],
				'actual' => count($params));
		}

		$val = $params[$pos];
		if (is_array($val)) {
			$val = cw_array_map('addslashes', $val);
			$val = implode("','", $val);
		}
		else {
			$val = addslashes($val);
		}

		$tokens[$k] = "'" . $val . "'";
		$pos ++;
	}

	return implode('', $tokens);
}

function db_escape_string($str) {
    global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

	return mysqli_real_escape_string($mysql_connection_id, $str);
}

#
# New DB API: Executing parameterized queries
# Example1:
#   $query = "SELECT * FROM table WHERE field1=? AND field2=? AND field3='\\?'"
#   $params = array (val1, val2)
#   query to execute:
#      "SELECT * FROM table WHERE field1='val1' AND field2='val2' AND field3='\\?'"
# Example2:
#   $query = "SELECT * FROM table WHERE field1=? AND field2 IN (?)"
#   $params = array (val1, array(val2,val3))
#   query to execute:
#      "SELECT * FROM table WHERE field1='val1' AND field2 IN ('val2','val3')"
#
# Warning:
#  1) all parameters must not be escaped with addslashes()
#  2) non-parameter symbols '?' must be escaped with a '\'
#
function db_exec($query, $params=array()) {
	global $config, $customer_id, $REMOTE_ADDR, $current_location;

	if (!is_array($params))
		$params = array ($params);

	$prepared = db_prepare_query($query, $params);

	if (!is_array($prepared)) {
		return db_query($prepared);
	}

	$error = "Query preparation failed";
	switch ($prepared['info']) {
	case 'mismatch':
		$error .= ": parameters mismatch (passed $prepared[actual], expected $prepared[expected])";
		break;
	case 'missing':
		$error .= ": parameter $prepared[param] is missing";
		break;
	}

	$msg  = "Site        : ".$current_location."\n";
	$msg .= "Remote IP   : $REMOTE_ADDR\n";
	$msg .= "Logged as   : $customer_id\n";
	$msg .= "SQL query   : $query\n";
	$msg .= "Description : ".$error;

	db_error_generic($query, $error, $msg);

	return false;
}

#
# Execute mysql query and store result into associative array with
# column names as keys
#
function cw_query($query, $mysql_connection_id = null) {
	$result = array();

	if ($p_result = db_query($query, $mysql_connection_id)) {
		while ($arr = db_fetch_array($p_result))
			$result[] = $arr;
		db_free_result($p_result);
	}
	return $result;
}

#
# Execute mysql query and store result into associative array with
# column names as keys and then return first element of this array
# If array is empty return array().
#
function cw_query_first($query) {
	if ($p_result = db_query($query)) {
		$result = db_fetch_array($p_result);
		db_free_result($p_result);
    }

    return is_array($result) ? $result : array();
}

#
# Execute mysql query and store result into associative array with
# column names as keys and then return first cell of first element of this array
# If array is empty return false.
#
function cw_query_first_cell($query, $mysql_connection_id = null) {
	if ($p_result = db_query($query, $mysql_connection_id)) {
		$result = db_fetch_row($p_result);
		db_free_result($p_result);
	}

	return is_array($result) ? $result[0] : false;
}

function cw_query_key($query, $column = 0) {
    $result = array();

    $fetch_func = is_int($column) ? 'db_fetch_row' : 'db_fetch_array';

    if ($p_result = db_query($query)) {
        while ($row = $fetch_func($p_result))
            $result[$row[$column]] = 1;
        db_free_result($p_result);
    }
    return $result;
}

function cw_query_column($query, $column = 0, $mysql_connection_id = null) {
	$result = array();

	$fetch_func = is_int($column) ? 'db_fetch_row' : 'db_fetch_array';

	if ($p_result = db_query($query, $mysql_connection_id)) {
		while ($row = $fetch_func($p_result))
			$result[] = $row[$column];

		db_free_result($p_result);
	}

	return $result;
}

#
# Insert array data to table
#
function cw_array2insert ($tbl, $arr, $is_replace = false, $fields = array()) {
	global $tables;

	if (empty($tbl) || empty($arr) || !is_array($arr))
		return false;

	if (!empty($tables[$tbl]))
		$tbl = $tables[$tbl];

	if ($is_replace )
		$query = "REPLACE";
	else
		$query = "INSERT";

    $fields = cw_check_field_names($fields, $tbl);
    foreach ($arr as $k => $v)
        if (!in_array($k, $fields)) unset($arr[$k]);

	$query .= " INTO $tbl (`" . implode("`, `", array_keys($arr)) . "`) VALUES ('" . implode("', '", $arr) . "')";

	$r = db_query($query);
	if ($r)
		return db_insert_id();

	return false;
}

#
# Update array data to table + where statament
#
function cw_array2update ($tbl, $arr, $where = '', $fields = array()) {
	global $tables;

	if (empty($tbl) || empty($arr) || !is_array($arr))
		return false;

	if ($tables[$tbl])
		$tbl = $tables[$tbl];

    $fields = cw_check_field_names($fields, $tbl);

	foreach ($arr as $k => $v) {
        if (array_key_exists($k, $fields)) $k = $fields[$k];
        elseif (!in_array($k, $fields)) continue;

		if (is_int($k)) {
			$r .= ($r ? ", " : "") . $v;
		} else {
			$r .= ($r ? ", `" : "`") . $k . "`='" . $v . "'";
		}
	}

    if (!$r) return false;

	$query = "UPDATE $tbl SET ". $r . ($where ? " WHERE " . $where : "");

	return db_query($query);
}

function cw_db_get_max_allowed_packet() {
    global $tables;

    $tmp = cw_query_first("SHOW VARIABLES LIKE 'max_allowed_packet'");
    return intval($tmp['Value']);
}

# kornev, we are checking the fields here, but we should think about the speed too actially
function cw_check_field_names($fields, $tbl) {
# kornev, if there was any fields defined - we should update only that
    if (is_array($fields) && count($fields)) return $fields;

    static $table_fields;
    if (!is_array($table_fields[$tbl]))
        $table_fields[$tbl] = array_keys(cw_query_hash("desc $tbl", 'Field'));
    return $table_fields[$tbl];
}

function cw_query_hash($query, $column = false, $is_multirow = true, $only_first = false) {
	$result = array();
	$is_multicolumn = false;

	if ($p_result = db_query($query)) {
		if ($column === false) {

			# Get first field name
			$c = db_fetch_field($p_result);
			$column = $c->name;

		} elseif (is_array($column)) {
			if (count($column) == 1) {
				$column = current($column);

			} else {
				$is_multicolumn = true;
			}
		}

		while ($row = db_fetch_array($p_result)) {

			# Get key(s) column value and remove this column from row
			if ($is_multicolumn) {

				$keys = array();
				foreach ($column as $c) {
					$keys[] = $row[$c];
					cw_unset($row, $c);
				}
				$keys = implode('"]["', $keys);

			} else {
				$key = $row[$column];
				cw_unset($row, $column);
			}

			if ($only_first)
				$row = array_shift($row);

			if ($is_multicolumn) {

				# If keys count > 1
				if ($is_multirow) {
					eval('$result["'.$keys.'"][] = $row;');

				} else {
					eval('$is = isset($result["'.$keys.'"]);');
					if (!$is) {
						eval('$result["'.$keys.'"] = $row;');
					}
				}

			} elseif ($is_multirow) {
				$result[$key][] = $row;

			} elseif (!isset($result[$key])) {
				$result[$key] = $row;
			}
		}

		db_free_result($p_result);
	}

	return $result;
}

# Generate SQL-query relations
function cw_db_generate_joins($joins, &$where, $parent = false) {
	$str = '';

	foreach ($joins as $jname => $j) {

		if ((!empty($parent) && $parent != $j['parent']) || (empty($parent) && !empty($j['parent'])))
			continue;

		$str .= cw_db_build_join($jname, $j, $where);
		unset($joins[$jname]);

		list($js, $tmp) = cw_db_generate_joins($joins, $where, (empty($j['tblname']) ? $jname : $j['tblname']));
		$str .= $tmp;
		$keys = array_diff(array_keys($joins), array_keys($js));
		if (!empty($keys)) {
			foreach ($joins as $k => $v) {
				if (in_array($k, $keys))
					unset($joins[$k]);
			}
		}
	}

	if (empty($parent) && !empty($joins)) {
# kornev, the left joins should be sorted by the parent
        foreach ($joins as $jname => $j)
            $joins[$jname]['key'] = $jname;
        uasort($joins, 'cw_db_sort_joins');
		foreach ($joins as $jname => $j)
			$str .= cw_db_build_join($jname, $j, $where);
		unset($joins);
	}

	if ($parent === false)
		return $str;
	else
		return array($joins, $str);
}

function cw_db_sort_joins($a, $b) {
    if ($a['parent'] == $b['key']) return 1;
    if ($b['parent'] == $a['key']) return -1;
    return ($a['pos'] < $b['pos']) ? -1 : 1; // key-parent sort works not always, pos - is an additional way to set priority
}

# Get [LEFT | INNER] JOIN string
function cw_db_build_join($jname, $join, &$where) {
	global $tables;

	$str = ' '.($join['is_inner'] ? 'INNER ' : ($join['is_straight']?'STRAIGHT_':($join['is_right']?'RIGHT ':'LEFT '))).'JOIN ';
	if (!empty($join['tblname'])) {
		$str .= $tables[$join['tblname']].' as '.$jname;
	} else {
		$str .= $tables[$jname];
	}
    if ($join['is_straight'])
        $where[] = $join['on'];
    else
    	$str .= ' ON '.$join['on'];

	return $str;
}

function cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys, $is_select = 1) {
    global $tables;

    $search_query = 'SELECT '.(is_array($fields)?implode(", ", $fields):$fields).' FROM ';

    if (!empty($from_tbls)) {
        foreach ($from_tbls as $k => $v)
            $from_tbls[$k] = $tables[$v].(is_int($k)?'':" as $k");
        $search_query .= implode(', ', $from_tbls);
    }

    $joins = array();
    if (is_array($query_joins))
    foreach ($query_joins as $ljname => $lj) {
        if ($is_select || !$lj['only_select'])
            $joins[$ljname] = $lj;
    }

    $search_query .= cw_db_generate_joins($joins, $where);

    if (!empty($where))
        $search_query .= " WHERE ".implode(" AND ", $where);
    if (!empty($groupbys))
        $search_query .= " GROUP BY ".implode(", ", $groupbys);
    if (!empty($having))
        $search_query .= " HAVING ".implode(" AND ", $having);
    if (!empty($orderbys))
        $search_query .= " ORDER BY ".implode(", ", $orderbys);

    return $search_query;
}
