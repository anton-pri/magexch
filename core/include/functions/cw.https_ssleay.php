<?php
# INPUT:

# $method		[string: POST|GET]

# $url			[string]
#	user:password@www.yoursite.com:443/path/to/script.asp

# $data			[array]
#	$data[] = "parametr=value";

# $join			[string]
#	$join = "\&";

# $cookie		[array]
#	$cookie = "parametr=value";

# $conttype		[string]
#	$conttype = "text/xml";

# $referer		[string]
#	$referer = "http://www.yoursite.com";

# $cert			[string]
#	$cert = "../certs/demo-cert.pem";

# $kcert		[string]
#	$keyc = "../certs/demo-keycert.pem";

# $rhead		[string]
#	$rhead = "...";

# $rbody		[string]
#	$rbody = "...";

function cw_https_request_ssleay($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="")
{
	global $config;
	global $app_main_dir;

	if ($method != "POST" && $method != "GET")
			return array("0","HTTPS: Invalid method");

	if (!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
			return array("0","HTTPS: Invalid URL");

	$perl_exe = cw_find_executable("perl",$config['General']['perl_binary']);
	if ($perl_exec === false)
			return array("0","HTTPS: perl is not found");

	$includes  = " -I".cw_shellquote($app_main_dir.'/payment');
	$includes .= " -I".cw_shellquote($app_main_dir.'/payment/Net');
	$execline = cw_shellquote($perl_exe).' '.$includes.' '.cw_shellquote($app_main_dir."/payment/netssleay.pl");

	$ui = parse_url($url);
	if (empty($ui['port'])) $ui['port'] = 443;

	$request = cw_https_prepare_request($method, $ui,$data,$join,$cookie,$conttype,$referer,$headers);
	$tmpfile = cw_temp_store($request);
	if (empty($tmpfile))
		return array(0, "HTTPS: cannot create temporaly file");

	$ignorefile = cw_temp_store("");
	$execline .= " $ui[host] $ui[port] ".cw_shellquote($cert).' '.cw_shellquote($kcert).' < '.cw_shellquote($tmpfile).' 2>'.cw_shellquote($ignorefile);

	$fp = popen($execline, "r");
	if (!$fp)
		return array(0, "HTTPS: Net::SSLeay execution failed");

	$res = cw_https_receive_result($fp);
	pclose($fp);
	@unlink($tmpfile);
	@unlink($ignorefile);

	return $res;
}

?>
