<?php
# INPUT:

# $method		[string: POST|GET]

# $url			[string]
#	www.yoursite.com:443/path/to/script.asp

# $data			[array]
#	$data[] = "parametr=value";

# $join			[string]
#	$join = "\&";

# $cookie		[array]
#	$cookie = "parametr=value";

# $conttype		[string]
#	$conttype = "text/xml";

# $referer		[string]
#	$conttype = "http://www.yoursite.com/index.htm";

# $cert			[string]
#	$cert = "../certs/demo-cert.pem";

# $kcert		[string]
#	$keyc = "../certs/demo-keycert.pem";

# $rhead		[string]
#	$rhead = "...";

# $rbody		[string]
#	$rbody = "...";

# [15:53][mclap@rrf:S4][~]$ openssl version
# OpenSSL 0.9.7a Feb 19 2003

function cw_https_request_openssl($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="")
{
	if ($method != "POST" && $method != "GET")
		return array("0","HTTPS: Invalid method");

	if (!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
		return array("0","HTTPS: Invalid URL");

	$openssl_binary = cw_find_executable("openssl");
	if (!$openssl_binary)
		return array("0","HTTPS: openssl executable is not found");

	if (!CW_IS_OS_WINDOWS)
		putenv("LD_LIBRARY_PATH=".getenv("LD_LIBRARY_PATH").":".dirname($openssl_binary));

	$ui = parse_url($url);

	// build args
	$args[] = "-connect $ui[host]:$ui[port]";
	if ($cert) $args[] = '-cert '.cw_shellquote($cert);
	if ($kcert) $args[] = '-key '.cw_shellquote($kcert);

	$request = cw_https_prepare_request($method, $ui,$data,$join,$cookie,$conttype,$referer,$headers);
	$tmpfile = cw_temp_store($request);
	$tmpignore = cw_temp_store('');

	if (empty($tmpfile))
		return array(0, "HTTPS: cannot create temporaly file");

	$cmdline = cw_shellquote($openssl_binary)." s_client ".join(' ',$args)." -quiet < ".cw_shellquote($tmpfile)." 2>".cw_shellquote($tmpignore);

	// make pipe
	$fp = popen($cmdline, "r");
	if( !$fp )
		return array(0, "HTTPS: openssl execution failed");

	$res = cw_https_receive_result($fp);
	pclose($fp);

	@unlink($tmpfile);
	@unlink($tmpignore);

	return $res;
}

?>
