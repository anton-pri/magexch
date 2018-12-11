<?php
define ('LOG_PHP_SIGNATURE', '<'.'?php die(); ?'.">\n");

function cw_log_add($label, $message, $add_backtrace=true, $stack_skip=0, $email_addresses=false, $email_only=false) {
	global $var_dirs;
	global $PHP_SELF;
	global $config;

    $label = str_replace('../','',$label);

	$filename = sprintf("%s/%s-%s.php", $var_dirs['log'], strtolower($label), date('ymd'));

	if ($label == 'SQL')
		$type = 'error';
	elseif ($label == 'INI' || $label == 'SHIPPING')
		$type = 'warning';
	else
		$type = 'message';

	$uri = $PHP_SELF;
	if (!empty($_SERVER['QUERY_STRING'])) $uri .= '?'.$_SERVER['QUERY_STRING'];

	if ($add_backtrace) {
		$stack = cw_get_backtrace(1+$stack_skip);
		$backtrace = "Request URI: $uri\nBacktrace:\n".implode("\n", $stack)."\n";
	}
	else
		$backtrace = '';

	if (is_array($message) || is_object($message)) {
		ob_start();
		print_r($message);
		$message = ob_get_contents();
		ob_end_clean();
	} else {
		$message = trim($message);
	}

	$local_time = "";
	if (!empty($config)) {
		$local_time = '(local: '.date('d-M-Y H:i:s', cw_core_get_time()).')';
	}

	$message = str_replace("\n", "\n    ", "\n".$message);
	$message = str_replace("\t", "    ", $message);

	$data = sprintf("[%s] %s %s %s:%s\n%s-------------------------------------------------\n",
		date('d-M-Y H:i:s'),
		$local_time,
		$label, $type,
		$message,
		$backtrace
	);

	cw_event('on_log_add', array($label, $data, $filename));

	if (!$email_only && cw_log_check_file($filename) !== false) {
        $fmode = ($label == 'bench_exec'?'w':'a+');
		$fp = @fopen($filename, $fmode);
		if ($fp !== false) {
			fwrite($fp, $data);
			fclose($fp);
		}
	}

	if (!empty($email_addresses) && is_array($email_addresses)) {
		cw_load('mail');

		foreach ($email_addresses as $k=>$email) {
			cw_send_simple_mail($config['Company']['site_administrator'], $email,
				$config['Company']['company_name'].": $label $type notification",
				$data);
		}
	}
}

function cw_log_flag($flag_key, $label, $message, $add_backtrace=false, $stack_skip=0) {
	static $email_addresses = false;
	global $config;

	if ($email_addresses === false && isset($config['Logging']['email_addresses'])) {
		$email_addresses = array_unique(explode('[ ,]+', $config['Logging']['email_addresses']));
	}

	$do_log =  empty($config);
	$addresses = false;
	$do_email = false;

	if (isset($config['Logging'][$flag_key])) {
		$value = $config['Logging'][$flag_key];
		$do_log = (strpos($value,'L') !== false);
		$do_email = (strpos($value,'E') !== false);
	}

	if ($do_email)
		$addresses = $email_addresses;

	if ($do_log || $do_email)
		cw_log_add($label, $message, $add_backtrace, $stack_skip+1, $addresses, ($do_email && !$do_log));
}

function cw_log_list_files($labels = false, $start=false, $end=false) {
	global $var_dirs;

	$regexp = '!^([a-zA-Z_-]+)-(\d{6})\.php$!S';

	$dp = @opendir($var_dirs['log']);
	if ($dp === false) return false;

	if ($start !== false)
		$start = (int)date('ymd', $start);
	else
		$start = 0;

	if ($end === false)
		$end = time() + 86400 * 30;

	$end = (int)date('ymd', $end);

	$return = array();

	if (!is_array($labels)) {
		if (!empty($labels))
			$labels = array (strtoupper($labels));
	}
	else {
		foreach ($labels as $k=>$v) {
			$labels[$k] = strtoupper($v);
		}
	}

	while ($file = readdir($dp)) {
		if (!preg_match($regexp, $file, $matches)) {
			continue;
		}

		$time_str = $matches[2];
		$ts = (int)$time_str;

		if ($ts < $start || $ts > $end) {
			continue;
		}

		$prefix = strtoupper($matches[1]);
		if ($labels !== false && is_array($labels) && !in_array($prefix, $labels)) {
			continue;
		}

		if (!isset($return[$prefix]))
			$return[$prefix] = array();

		$time_ts = mktime(0,0,0, substr($time_str,2,2), substr($time_str,4,2), substr($time_str,0,2));

		$return[$prefix][$time_ts] = $file;
	}

	foreach ($return as $prefix=>$data) {
		ksort($return[$prefix]);
	}

	return $return;
}

function cw_log_get_contents($labels = false, $start=false, $end=false, $html_safe=false, $count=0) {
	global $var_dirs;
	static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

	$logs = cw_log_list_files($labels, $start, $end);

	if (empty($logs)) return false;

	$logs_data = array();

	if ($count < 0) $count = 0;

	foreach ($logs as $label=>$data) {
		$contents = "";
		$records = array();
		foreach ($data as $ts=>$file) {
			$fp = @fopen($var_dirs['log'].'/'.$file, "rb");
			if ($fp !== false) {
				fseek($fp, strlen(LOG_PHP_SIGNATURE), SEEK_SET);
				$buffer = '';
				while (($line = fgets($fp, 8192)) !== false) {
					if (!$count) {
						$contents .= $line;
						continue;
					}

					if (preg_match($regexp, $line)) {
						if (!empty($buffer)) {
							$records[] = $buffer;
							if (count($records) > $count) array_splice($records, 0, -$count);
						}

						$buffer = $line;
					}
					else {
						$buffer .= $line;
					}
				}

				if (!empty($buffer)) {
					$records[] = $buffer;
					if (count($records) > $count) array_splice($records, 0, -$count);
				}

				fclose($fp);
			}
		}

		if (!empty($records)) {
			$contents .= implode('', $records);
			$records = false;
		}

		if ($html_safe) {
			$contents = htmlspecialchars($contents,ENT_IGNORE);
			$contents = str_replace('  ', '&nbsp ', $contents);
		}

		if (!empty($contents)) {
			$logs_data[$label] = $contents;
		}
	}

	return $logs_data;
}


function cw_log_count_messages($labels=false, $start=false, $end=false) {
	global $var_dirs;
	static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

	$logs = cw_log_list_files($labels, $start, $end);

	if (!is_array($logs) || empty($logs))
		return false;

	$return = array();

	foreach ($logs as $label=>$list) {
		if (!is_array($list) || empty($list)) continue;

		foreach ($list as $timestamp=>$file) {
			# count records in single log file
			$fp = @fopen($var_dirs['log'].'/'.$file, 'r');
			if ($fp === false)
				continue;

			$count = 0;
			while (($line = fgets($fp, 8192)) !== false) {
				if (preg_match($regexp, $line)) $count++;
			}

			fclose($fp);

			$return[$label][$timestamp] = $count;
		}
	}

	return $return;
}

function cw_log_get_names($labels=false, $force_output=false) {
	static $all_labels = false;

	if ($all_labels === false) {
		$all_labels = array (
			'DATABASE' => 'lbl_log_database_operations',
			'FILES' => 'lbl_log_file_operations',
			'ORDERS' => 'lbl_log_orders_operations',
			'PRODUCTS' => 'lbl_log_products_operations',
			'SHIPPING' => 'lbl_log_shipping_errors',
			'PAYMENTS' => 'lbl_log_payment_errors',
			'PHP' => 'lbl_log_php_errors',
			'SQL' => 'lbl_log_sql_errors',
			'ENV' => 'lbl_log_env_changes',
			'DEBUG' => 'lbl_log_debug_messages',
			'DECRYPT' => 'lbl_decrypt_errors',
			'BENCH' => 'lbl_log_bench_reports'
		);
	}

	if ($force_output !== false && $fource_output !== true)
		$force_output = false;

	$keys = array_keys($all_labels);
	if (empty($labels) || !is_array($labels))
		$labels = $keys;
	else {
		$labels = array_intersect($labels, $keys);
		if (empty($labels))
			$labels = $keys;
	}

	$result = array ();
foreach ($labels as $label) {
	$result[$label] = cw_get_langvar_by_name($all_labels[$label], NULL, false, $force_output);
}

return $result;
}

function cw_log_check_file($filename) {
$fp = @fopen($filename, "a+");
if ($fp === false) return false;

if (filesize($filename) ==0) {
	@fwrite($fp, LOG_PHP_SIGNATURE);
	@fclose($fp);
	return $filename;
}

if (@fseek($fp, 0, SEEK_SET) < 0) {
	@fclose($fp);
	return false;
}

$tmp = @fread($fp, strlen(LOG_PHP_SIGNATURE));
if (strcmp($tmp, LOG_PHP_SIGNATURE)) {
	@fseek($fp, 0, SEEK_SET);
	@ftruncate($fp, 0);
	@fwrite($fp, LOG_PHP_SIGNATURE);
}
@fclose($fp);

return $filename;
}

function cw_array_compare($orig, $new) {
$result = array (
	'removed' => false,
	'added' => false,
	'delta' => false,
	'changed' => false
);

$keys = array();
if (is_array($orig)) $keys = array_keys($orig);
if (is_array($new)) $keys = array_merge($keys, array_keys($new));
$keys = array_unique($keys);

foreach ($keys as $key) {
	$in_orig = isset($orig[$key]);
	$in_new = isset($new[$key]);

	if ($in_orig && !$in_new) {
		$result['removed'][$key] = $orig[$key];
	}
	elseif (!$in_orig && $in_new) {
		$result['added'][$key] = $new[$key];
	}
	else {
		# check for changed values
		if (!is_array($new[$key])) {
			if (!strcmp((string)$orig[$key], (string)$new[$key])) {
				continue;
			}

			$is_numeric = preg_match('!^((\d+)|(\d+\.\d+))$!S', $new[$key]);

			if ($is_numeric) {
				$result['delta'][$key] = $new[$key] - $orig[$key];
			}

			$result['changed'][$key] = $new[$key];
		}
		else {
			$tmp = cw_array_compare($orig[$key],$new[$key]);

			foreach ($tmp as $tmp_key=>$tmp_value) {
				if ($tmp_value === false) continue;

				$result[$tmp_key][$key] = $tmp_value;
			}
		}
	}
}

# remove not used arrays
foreach ($result as $k=>$v) {
	if ($v === false)
		unset($result[$k]);
}

return $result;
}

#
# Function to get backtrace for debugging
#
function cw_get_backtrace($skip=0) {
$result = array();
if (!function_exists('debug_backtrace')) {
	$result[] = '[cw_get_backtrace() is supported only for PHP version 4.3.0 or better]';
	return $result;
}
$trace = debug_backtrace();

if (is_array($trace) && !empty($trace)) {
	if ($skip>0) {
		if ($skip < count($trace))
			$trace = array_splice($trace, $skip);
		else
			$trace = array();
	}

	foreach ($trace as $item) {
		if (!empty($item['file']))
			$result[] = $item['file'].':'.$item['line'];
	}
}

if (empty($result)) {
	$result[] = '[empty backtrace]';
}

return $result;
}

#
# Error handler
#
function cw_error_handler($errno, $errstr, $errfile, $errline) {
    static $hash_errors = array();

    if (!(ini_get("error_reporting") & $errno)) return;

    if (ini_get("display_errors") == 0 && ini_get("log_errors") == 0) return;

    if (ini_get("ignore_repeated_errors") == 1 && isset($hash_errors[$errno]) && isset($hash_errors[$errno][$errfile.":".$errline])) return;

    $date = date('d-M-Y H:i:s', cw_core_get_time());

    $errortypes = array(
	E_ERROR				=> "Error",
	E_WARNING			=> "Warning",
	E_PARSE				=> "Parsing Error",
	E_NOTICE			=> "Notice",
	E_CORE_ERROR		=> "Error",
	E_CORE_WARNING		=> "Warning",
	E_COMPILE_ERROR		=> "Error",
	E_COMPILE_WARNING	=> "Warning",
	E_USER_ERROR		=> "Error",
	E_USER_WARNING		=> "Warning",
	E_USER_NOTICE		=> "Notice",
	E_STRICT			=> "Runtime Notice"
    );

    $errortype = isset($errortypes[$errno]) ? $errortypes[$errno] : "Unknown Error";

    if (ini_get("display_errors") != 0) {

	    # Display error
    	global $REQUEST_METHOD;
	    if (empty($REQUEST_METHOD))
		    echo "$errortype: $errstr in $errfile on line $errline\n";
    	else
	    	echo "<b>$errortype</b>: $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
    }

    if (ini_get("log_errors") == 1 && ini_get("error_log") != '') {
    	# Write error to file
	    $bt = '';
    	$bt = "\nREQUEST_URI: ".$_SERVER['REQUEST_URI'];
		$bt .= "\nBacktrace:\n\t".implode("\n\t", cw_get_backtrace(1));

    	error_log(
	    	"[$date] $errortype: $errstr in $errfile on line $errline $bt\n",
		    3,
    		ini_get("error_log")
	    );
    }

    if (ini_get("ignore_repeated_errors") == 1) {
	    if (!isset($hash_errors[$errno])) $hash_errors[$errno] = array();
    	$hash_errors[$errno][$errfile.":".$errline] = true;
    }
}

set_error_handler("cw_error_handler");

# Set internal php values
if (DEBUG_MODE==2 || DEBUG_MODE==0) {
    ini_set("display_errors",0);
    ini_set("display_startup_errors",0);
}
if (DEBUG_MODE==2 || DEBUG_MODE==3) {
    ini_set("log_errors", 1);
    ini_set("error_log", cw_log_check_file($var_dirs['log']."/php-".date('ymd').".php"));
    ini_set("ignore_repeated_errors", 1);
}

# Remove empty log for previous day. Purging/checking all empty logs from
# previuos days can reduce performance
$_prev_logfile = $var_dirs['log']."/php-".date('ymd', time()-SECONDS_PER_DAY).".php";
if (file_exists($_prev_logfile) && @filesize($_prev_logfile) <= strlen(LOG_PHP_SIGNATURE))
@unlink($_prev_logfile);

# Log uploaded files
if (!empty($_FILES)) {
    $_lines = array();
    foreach ($_FILES as $_k=>$_v) {
	    if (empty($_v['name'])) continue;
	    $_lines[] = $_v['name'].' (size: '.$_v['size'].' byte(s), type: '.$_v['type'].')';
    }

    if (!empty($_lines))
	    cw_log_add('FILES', "Uploaded files:\n".implode($_lines));
}
?>
