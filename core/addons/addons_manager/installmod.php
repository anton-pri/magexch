<?php
global $sets;
global $types;

define('TARGET_URL', $_SERVER['REDIRECT_URL'].'?target=installmod');

$applications = array(
  'xc' => array(
      'name' => 'XC',
      'installservice' => "http://www.cartworks.com/store/services/mod-install/",
      'userguidespages' => "http://www.cartworks.com/store/user-guides/",
      ),
  'cw' => array(
      'name' => 'CartWorks',
      'installservice' => "http://www.cartworks.com/store/services/mod-install/",
      'userguidespages' => "http://www.cartworks.com/store/user-guides/",
      ),
);

$sets = array();
$sets["app"] = "cw"; # [xc|cw]
$sets["src"] = "../../../"; # RELATIVE PATH TO a SOFTWARE (CURRENT DIR is "", subdir is "..", neighbor one - "../neighor")
$sets["supportemail"] = "install@cartworks.com";
#$sets["supportemail_from"] = "";
$sets["expert"] = 0;
$sets["skipmysqlcheck"] = 0;
$sets["skipfailedpatch"] = 1;
$sets["skipwwwcopy"] = 1;
$sets["showdebugauthcode"] = 1;

$sets["silentverbose"] = 0;

$addonname = &cw_session_register('addonname');

if (empty($mod)) {
    $mod = $addonname;
} else {
    $addonname = $mod;
}

$title_path = "http://dev.cartworks.com/iscript/mod_titles.php?mdr=".$mod;
if ($stream = fopen($title_path, 'r')) {
    if ($mod_title = stream_get_contents($stream))
        $sets["mod"] = $mod_title;
    fclose($stream);
}
$mods_titles = array(
);
if (empty($sets["mod"]) && in_array($mod, $mods_titles)) {
    $sets["mod"] = $mods_titles[$mod];
}
if (empty($sets["mod"]))
    $sets["mod"] = "CartWorks.com with codename <b>'$mod'</b>";

#############################################################################
# DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
# YOU REALLY KNOW WHAT YOU ARE DOING 
#############################################################################

set_time_limit(3600);
$x_error_reporting = E_ALL ^ E_NOTICE;
error_reporting ($x_error_reporting);

header("content-type: text/html");

if (!get_magic_quotes_gpc())
foreach(array("GET","POST","COOKIE") as $__avar)
	foreach (${"_".$__avar} as $__var => $__res)
		${"_".$__avar}[$__var] = i_addslashes($__res);

foreach(array("GET","POST","COOKIE") as $__avar)
	foreach (${"_".$__avar} as $__var => $__res)
		$$__var = $__res;

#############################################################################

$sets["real"] = i_norm_dir(strtr(realpath(dirname(__FILE__)),array("\\"=>"/")));
$sets["src"]  = i_norm_dir(strtr(realpath($sets["real"].i_norm_dir($sets["src"])),array("\\"=>"/")));
//$sets["dist"] = $sets["real"];
$sets["dist"] = $sets["src"].'var/tmp/addons/'.$mod.'/';
$sets["time"] = date("Ymd-His");

if(!is_dir($sets["src"]))
{
	print "Unable to find the application folder [<b>".$sets["src"]."</b>]";
	exit;
}

if($sets["app"]=="xc") # xcart detection
{
	if(	file_exists($sets["src"]."top.inc.php") && 
	  	file_exists($sets["src"]."config.php") &&
		file_exists($sets["src"]."init.php") &&
		is_dir($sets["src"]."var")
	)
	{
		$sets["app_"] = $applications[$sets["app"]]['name'];
		$sets["tmp"] = $sets["src"].i_norm_dir("var/iscript");
	}
}
elseif($sets["app"]=="cw") # cartworks
{
    $sets["app_"] = $applications[$sets["app"]]['name'];
    $sets["tmp"] = $sets["src"].i_norm_dir("var/log");
}
else
{
	print "app=".$sets["app"]."(unknown): I do not know how to do it";
	exit;
}

if(!$sets["app_"])
{
	print "Unable to find the application files [<b>".$sets["src"]."</b>]";
	exit;
}


# autodetect
/*
if($app=="echo")
{
	echo md5($sets["mod"].$_SERVER["REMOTE_ADDR"].$sets["app"]);
	exit;
}
elseif($app)
{
	list($err,$_r) = i_http_get("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."&app=echo");
	if(trim($_r)!=md5($sets["mod"].$_SERVER["REMOTE_ADDR"].$sets["app"]))
		die("NoIP"); # protection. it can be hacked only only in case a hacker's PC is the same machine as the server

	if($app=="xc")
	{
		chdir($sets["src"]);
		@define('XCART_EXT_ENV',1);
		include "./top.inc.php";
		include "./init.php";
		foreach(array("sql_host", "sql_user", "sql_db", "sql_password") as $key)
			print $key.'='.urlencode($$key).'&';
		print 'appver='.$config["version"].'&auth='.($installation_auth_code?$installation_auth_code:$license);
		exit;
	}
	elseif($app=="cw")
	{
        $app_config_file = parse_ini_file('./include/config.ini', true);
        $_app_config_file = @parse_ini_file('./include/config.local.ini', true);
        if (!empty($_app_config_file)) $app_config_file = $_app_config_file;
		foreach(array("host", "user", "db", "password") as $key)
			print 'sql_'.$key.'='.urlencode($app_config_file['sql'][$key]).'&';
        print 'appver=1&auth=AUTH';
		exit;
	}
}
*/

if($support)
{
	header("content-type: text/xml");
	$f = $sets["tmp"]."report".$support.".log";
	if(file_exists($f))
	{
		$f = file($f);
                #$sets["supportemail_from"] = $sets["supportemail_from"]?$sets["supportemail_from"]:$sets["supportemail"];
		$sets["supportemail_from"] = $helpemail; # from the _GET
                if (!preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $sets["supportemail_from"], $m))
                        $m = array();

                $headers = "From: ".$sets["supportemail_from"]."\nReply-to: ".$sets["supportemail_from"]."\n";
                $headers.= "X-Mailer: iScript\nMIME-Version: 1.0\nContent-Type: text/plain\n";
                @mail($sets["supportemail"],"Email-Subject-".$sets["mod"],$helpmsg."\n\n-----------------------\n\n".var_export($sets,1)."\n\n-----------------------\n\n".join("",$f),$headers,$m[1]?"-f".$m[1]:"");

		print "<root>OK</root>";
	}
	else
		print "<root>NOK</root>";
	exit;
}
/*
if($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["auth_code_posted"])
{
	$tmp = strtoupper(md5($_POST["auth"].$_SERVER["REMOTE_ADDR"]));
	cw_set_cookie("iscriptauth", $tmp, time()+3600);
	header("Location: ".$_SERVER["REQUEST_URI"].($_SERVER["QUERY_STRING"]?"?".$_SERVER["QUERY_STRING"]:""));
	exit;
}

 */
/*********************** design ***********************/

define("OK", "OKI!");
define("NOK","NOK!");
define("NFO","NFO!");
define("WRN","WRN!");
define("NTG","NTG!");
define("EXC","EXC!");

$types = array(
	""    => array(" ",'<div class="hr'.($sets["expert"]?"ex":"").'"></div>',""),
	"OKI" => array("+",'<img src="http://www.cartworks.com/store/iscript/images/oki.png" title="+" />','oki'),
	"NOK" => array("-",'<img src="http://www.cartworks.com/store/iscript/images/nok.png" title="-" />','nok'),
	"NFO" => array("*",'<img src="http://www.cartworks.com/store/iscript/images/nfo.png" title="*" />','nfo'),
	"WRN" => array("!",'<img src="http://www.cartworks.com/store/iscript/images/wrn.png" title="!" />','wrn'),
	"EXC" => array("!",'<img src="http://www.cartworks.com/store/iscript/images/excl.png" title="!" />','wrn'),
	"NTG" => array(" ",' ',' '),
);


$modes = array(
	"" => "1/3. <span>".$sets["mod"]."</span> :: Pre-check",
	"apply" => "2/3. <span>".$sets["mod"]."</span> :: Installation",
	"confirm" => "3/3. <span>".$sets["mod"]."</span> :: Post-check",
);



?>
<html xml:lang="en-us" xmlns="http://www.w3.org/1999/xhtml" lang="en-us">
<head>
<title>CartWorks.com addon Installation</title>
<style type="text/css"">
area, base, basefont, head, meta, script, style, title, noembed, param {
display:none;
}
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {
background:none repeat scroll 0 0 transparent;
border:0 none;
font-size:100%;
margin:0;
outline:0 none;
padding:0;
vertical-align:baseline;
}
body {
/*background:none repeat scroll 0 0 #EFEFEF;*/
margin: 0pt;
color:#333333;
font:12px "Lucida Grande",Verdana,Verdana,Arial,Helvetica,sans-serif;
text-align:center;
}
div.sts { width:30px; text-align:center;float:left; }
div.oki { background-color: #00ff00; }
div.nok { background-color: #ff0000; }
div.nfo { background-color: #bbbbbb; }
div.wrn { background-color: #0000ff; }
form { display: inline; }
.container {
margin:0 auto;
text-align:left;
}
.clear {
clear:both !important;
display:block;
}
p {
margin:8px 0;
padding:0;
}
a {
color:#845314;
text-decoration:underline;
}
a:hover {
text-decoration:none;
}
#page {
position:relative;
z-index:50;
}
#page .container {
border:0 none;
}
#page .container {
background:none repeat scroll 0 0 #FFFFFF;
}
#page .container_inner {
padding:0 0 20px;
}
.content_fix {
display:none;
}
#page_content {
padding:18px 40px 0;
}
#dashboard {
overflow:hidden;
}
#dashboard_sections {
width:100%;
}
.top_tabs {
height:28px;
list-style:none outside none;
margin:0;
overflow:visible;
padding:0 10px 0 0;
float:right;
}
.top_tabs li {
-moz-border-radius:5px 5px 0 0;
}
.title {
	width:50%;
	padding-left:10px;
	font-size:100%;
	float: left;
	height:27px;
	line-height:27px;
}
.title span {
	font-size:130%;
	padding:0 5px;
	font-weight: bold;
}
.top_tabs li {
background-position:left top;
background-repeat:no-repeat;
border-left:1px solid #F1F1F1;
border-right:1px solid #F1F1F1;
border-top:1px solid #F1F1F1;
cursor:pointer;
display:block;
float:left;
height:27px;
line-height:27px;
margin-right:3px;
}
.top_tabs li.selected {
background:none repeat scroll 0 0 #FAFAFA;
border-color:#CCCCCC #CCCCCC #FAFAFA;
border-style:solid;
border-width:1px;
position:relative;
top:1px;
}
.top_tabs li a {
color:#999999;
}
.top_tabs li a:hover, .top_tabs li.selected a:hover {
color:#333333;
}
.top_tabs li.selected a {
color:#000000;
font-weight:bold;
}
.site_logo {
float:left;
margin-left:6px;
margin-top:18px;
}
.top_tabs li a {
background-position:right top;
background-repeat:no-repeat;
cursor:pointer;
display:block;
float:left;
font-size:11px;
height:27px;
line-height:27px;
padding:0 10px;
}
#dashboard_sections .top_tabs_object_list {
clear:both;
margin-top:0 !important;
}
.dashboard_wide_sidebar.alt {
background:none repeat scroll 0 0 #FAFAFA;
border-color:#DDDDDD;
}

.dashboard_wide_sidebar {
-moz-border-radius:5px 5px 5px 5px;
border-bottom-width:2px;
border-right-width:2px;
}
.dashboard_wide_sidebar {
border-style:solid;
border-width:1px 2px 2px 1px;
margin:10px 0;
overflow:hidden;
}
.dashboard_wide_sidebar .dashboard_wide_sidebar_inner_2 {
overflow:hidden;
padding:10px 15px;
}
#dashboard #progres_ {
font-size:12px;
line-height:17px;
}
#dashboard #progres_ img {
height:16px;
margin:0;
padding:0 3px 0 0;
vertical-align:middle;
width:16px;
}
img {
border:0 none;
}
#page_actions {
float:right;
margin-top: 10px;
padding:0;
}
#page_actions li {
display:inline;
float:left;
margin:0;
position:relative;
}
#page_actions li.single a {
border-right:0 none;
margin-left:10px;
}
#page_actions a {
border:0 none;
}
#page_actions a {
background-color: #93BC51;
border: 1px solid #84B43E;
border-radius: 2px 2px 2px 2px;
color: #FFFFFF;
font: bold 16px Helvetica;
height: auto;
padding: 8px 30px;
text-transform: uppercase;
cursor:pointer;
display:block;
float:left;
text-decoration:none;
vertical-align:middle;
}
#page_actions a span {
    background-color: #93BC51;
    background-image: none;
    font: bold 16px Helvetica;
    height: auto;
    padding: 0;
display:block;
float:left;
font-weight:bold;
overflow:hidden;
padding:0 10px;
}

#progres_ span {
	display: block;
	float: left;
}
div.hr {
	border-bottom:1px solid #F1F1F1;
	padding:5px 0;
}
#progres_ div.hrex {
	border-bottom:1px solid #F1F1F1;
	padding:5px 0 0 0;
	margin:0 0 5px 0;
}

</style>
</head>    
<body <?php if ($mode == "confirm") echo "onload='javascript: scroll(0,0)'"; ?> >
 

<div id="page">
<div class="container">
<div class="container_inner">
<div id="page_content">
    		        
<div id="dashboard"><div id="dashboard_sections">

<div class="title"><?php echo $modes[$mode].($sets["expert"]?". Expert mode":""); ?></div>
<div class="top_tabs_object_list dashboard_wide_sidebar alt"><div class="dashboard_wide_sidebar_inner"><div class="dashboard_wide_sidebar_inner_2">
<div style="display: block;" class="dashboard_section_content">
            
		<div id="progres_" style="min-height:300px;padding:2px;margin:2px;<?php echo $sets["expert"]?"font-size:11px;font-family:monospace;white-space:pre;":""; ?>"></div>

</div>
</div></div></div>

<div id="support_" style="padding:0;margin:0;display:none;">
<div class="top_tabs_object_list dashboard_wide_sidebar alt"><div class="dashboard_wide_sidebar_inner"><div class="dashboard_wide_sidebar_inner_2">
<div style="display: block;" class="dashboard_section_content">

<div id="sp">
<div style="float:left;text-align:justify;width:80%;">
If you're experiencing issues with our installation script you can send the installation log to our support team.<br />
</div>

<ul id="page_actions">
<li class="single without_subitems">
<a href="javascript:sp();"><span>Email the Log File</span></a>  	        
</li>
</ul>
</div>

<div id="sping" style="display:none;">
<?php echo $types["NFO"][1]; ?> Sending...
</div>

<div id="spfrm" style="display:none;">

<?php echo $types["NFO"][1]; ?> Please enter your email address and a summary of the issue you've experienced.<br />
<div class="hr"></div>
<br />

<div style="width:100px;float:left;display:inline;"><?php echo $types["NFO"][1]; ?> Email:</div>
<div style="float:left;"><input id="helpemail" type="text" size="20" onBlur="javascript:speml(this.value);"></div>
<div style="float:left;padding-left:4px;" id="helpemailerr"> <?php echo $types["NOK"][1]; ?> Email is wrong</div>
<div class="clear"></div>

<div style="width:100px;float:left;display:inline;"><?php echo $types["NFO"][1]; ?> Message:</div>
<div style="float:left;"><textarea id="helpmsg" cols="60" rows="5" onBlur="javascript:spmsg(this.value);"></textarea></div>
<div style="float:left;padding-left:4px;" id="helpmsgerr"> <?php echo $types["NOK"][1]; ?> Please drop us a line</div>
<div class="clear"></div>

<ul id="page_actions" style="float:none;padding-left:100px;">
<li class="single without_subitems">
<a href="javascript:spfrm();"><span>Send the Log File</span></a>  	        
</li>
</ul>

</div>

<div id="spok" style="display:none;">
<?php echo $types["OKI"][1]; ?> Thank you. The email has been sent. We will contact you soon.
</div>

<div id="spnok" style="display:none;">
<?php echo $types["NOK"][1]; ?> Sorry, we are unable to find this report.
</div>

</div>
</div></div></div>
</div>

</div></div>

</div>
	<div class="clear"></div>
</div>
	<div class="content_fix"></div>
</div>
</div>
    
<script>

function ht(u,f)
{
	http_request = false;

	if (window.XMLHttpRequest)
	{ // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) { http_request.overrideMimeType('text/xml'); }
	}
	else if (window.ActiveXObject)
	{ // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP"); }
		catch (e) { try { http_request = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {} }
	}

	if (!http_request) { alert('Cannot create an XMLHTTP instance'); return false; }

	eval("http_request.onreadystatechange = " + f + ";");
	http_request.open('GET',u, true);
	http_request.send(null);
	return true;
}

function rs()
{
	if (http_request.readyState == 4 && http_request.status == 200)
	{
		document.getElementById('sping').style.display = 'none';
		var xmldoc = http_request.responseXML;
		var node = xmldoc.getElementsByTagName('root');
		if(node[0].firstChild.data=="OK")
			document.getElementById('spok').style.display = '';
		else
			document.getElementById('spnok').style.display = '';
	}
}

function sp()
{
	document.getElementById('sp').style.display = 'none';
	document.getElementById('spfrm').style.display = '';
	document.getElementById('helpemail').focus();
}

function speml(helpemail)
{
	t = eml(helpemail);
	document.getElementById('helpemailerr').style.display = t ? 'none' : '';
	if(!t)
		document.getElementById('helpemail').focus();
	return t ? 0 : 1;
}

function spmsg(helpmsg)
{
	document.getElementById('helpmsgerr').style.display = helpmsg ? 'none' : '';
	if(!helpmsg)
		document.getElementById('helpmsg').focus();
	return helpmsg ? 0 : 1;
}

function spfrm()
{
	err = 0;

	he = document.getElementById('helpemail').value;
	err += speml(he);

	hm = document.getElementById('helpmsg').value;
	err += spmsg(hm);

	if (err)
		alert('Please check all fields');

	if(!err)
	if(ht('<?php echo TARGET_URL."&helpemail='+encodeURIComponent(he)+'&helpmsg='+encodeURIComponent(hm)+'&support=".$sets["time"].($mode?$mode:"init"); ?>','rs'))
	{
		document.getElementById('spfrm').style.display = 'none';
		document.getElementById('sping').style.display = '';
	}
}

function eml(eml)
{
	return eml.search(/^[A-Za-z0-9]{1}([A-Za-z0-9_\-\.]*)[^\.]*\@[^\.]([A-Za-z0-9_\-\.]+\.)+[A-Za-z]{2,6}[ ]*$/gi) != -1;
}

function pl(t)
{
	p.innerHTML += t;
	window.scroll(0, 500000);
}

p = document.getElementById('progres_');

</script>
<?php
flush(); # let's show it...
/*********************** /design ***********************/
/*********************** step 1 ***********************/

@ini_set("track_errors",1);

if ($mode != "apply" && $mode != "confirm")
	i_verbose(OK."Found an application folder [<b>".$sets["src"]."</b>]", false);

list($nok,$cmds) = i_make_dir($sets["tmp"]);

if ($mode != "apply" && $mode != "confirm")
	i_verbose(($nok?NOK."Unable to create":OK."Created")." a temp folder [<b>".$sets["tmp"]."</b>]", false);

if($nok)
{
	i_verbose("Please create the folder and/or change its permissions to writable ones!",false);
	i_verbose("<i>".join("<br>",$cmds)."</i>",false);
	i_exit(false);
}

$ok = 0;
$sets["logfile"] = $sets["tmp"]."report".$sets["time"].($mode?$mode:"init").".log";
if($log = @fopen($sets["logfile"],"wt"))
{
	fclose($log);
	@chmod($sets["logfile"],0666);
	i_verbose("","The log is started at ".date("H:i:s d.m.Y"));

if ($mode != "apply" && $mode != "confirm")
	i_verbose("",OK."Found an application folder [<b>".$sets["src"]."</b>]");

	$ok = 1;
}

if ($mode != "apply" && $mode != "confirm")
	i_verbose(($ok?OK."Created":NOK."Unable to create")." a logfile [<b>".$sets["logfile"]."</b>]", false);

if(!$ok)
{
	i_verbose("Please check what you can do with the following error:",false);
	i_verbose("<i>".$php_errormsg."</i>",false);
	i_exit(false);
}

if (empty($mod)) {
    i_verbose(NOK."Installation cannot run without addon specification",false);
    i_exit(false);
}

if($mode=="apply")
{
	$sets["backup"] = $sets["tmp"]."backup".$sets["time"];
	list($nok,$cmds) = i_make_dir($sets["backup"]);
//	i_verbose(($nok?NOK."Unable to create":OK."Created")." a backup folder [<b>".$sets["backup"]."</b>]");
	if($nok)
	{
		# I see no reason to do not create a folder here in case the log file is created (c) sdg
		i_verbose("Please check what you can do with the following error:");
		i_verbose("<i>".$php_errormsg."</i>");
		i_exit();
	}
}


i_verbose(" ");
/*
# app detection
$url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."&app=cw";
i_verbose("","Run autodetect: ".$url);
list($err,$_r) = i_http_get($url);
$_r = trim($_r);
i_verbose('',"Get response [<b>".$err["ERROR"]."</b>]");
//i_verbose('',strtr($_r,array("\n"=>"","\r"=>"","&"=>" ")));
parse_str($_r,$t);
$sets = array_merge($t,$sets); # the given data should not overwrite our settings
# /app detection
*/
$sets["sql_host"] = $app_config_file['sql']['host'];
$sets["sql_db"] = $app_config_file['sql']['db'];
$sets["sql_user"] = $app_config_file['sql']['user'];
$sets["sql_password"] = $app_config_file['sql']['password'];

$_ver = parse_ini_string(file_get_contents("http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?target=version"), true);
$sets['appver'] = $_ver['version']['version'];

$sets['auth'] = 'AUTH';

if(!$sets["appver"] || !$sets["sql_db"] || !$sets["auth"])
	i_exit("Unable to detect a version of the application and a name of the used database");

if ($mode != "apply" && $mode != "confirm")
	i_verbose("Found installation addon for [<b>".$sets["mod"]."</b>]");

if ($mode != "apply" && $mode != "confirm")
	i_verbose("Found application [<b>".$sets["app_"]." ver. ".$sets["appver"]."</b>]");

if($appver)
{
	$sets["appver"] = $appver;
	if ($mode != "apply" && $mode != "confirm")
		i_verbose("Chosen version of the application [<b>".$sets["app_"]." ".$sets["appver"]."</b>]");
}

/*
#i_verbose("[".$sets["islasthr"]."!".$sets["auth"]."]");
$ok = strtoupper(md5($sets["auth"].$_SERVER["REMOTE_ADDR"]));
if($ok!=$_COOKIE["iscriptauth"])
{
	i_verbose(" ",false);
	i_verbose("To continue with install of the addon please enter your authcode, this is found in the <a href='../admin/general.php' target='_blank'>Summary section</a> of your X-Cart admin area.",false);
        if ($sets["showdebugauthcode"]) $debug_auth_code = "(DEBUG: ".$sets["auth"].")";
	i_exit(($_COOKIE["iscriptauth"]?NOK."The entered authcode is wrong. <br />".$types["NFO"][1]:"")."<form name='authform' method='POST'>Please enter authcode: <input type='text' name='auth' value='' /><input type='hidden' name='auth_code_posted' value='1' /><input type='button' value='Submit' onclick='jabascript: document.authform.submit();' /></form> $debug_auth_code");
}
*/
	i_verbose(" ");

	$ok = is_dir($sets["dist"]);

if ($mode != "apply" && $mode != "confirm")
	i_verbose(($ok?OK."Found":NOK."Unable to find")." distributive [<b>".$sets["dist"]."</b>]");
	if(!$ok)
	{
		i_verbose("Please upload a distributive what you want to install to [<b>".$sets["dist"]."</b>]");
		i_verbose("Please be sure there is [<b>patches</b>] folder inside.");
		i_exit();
	}	

$sets["distnew"] = $sets["dist"].preg_replace("/\D/","",$sets["appver"])."/new_files/";
$ok = is_dir($sets["distnew"]);
if (!$ok) $sets["distnew"] = $sets["dist"].$sets["appver"]."/new_files/";
$ok = is_dir($sets["distnew"]);
if ($mode != "apply" && $mode != "confirm")
	i_verbose(($ok?OK."Found":WRN."Unable to find")." New Files [<b>".$sets["distnew"]."</b>]"); # it's not an error when we have not new files
if(!$ok)
	$sets["distnew"] = "";

$sets["distsql"] = $sets["dist"]."patches/patch-".$sets["appver"].".sql";
$ok = is_file($sets["distsql"]);
if ($mode != "apply" && $mode != "confirm")
	i_verbose(($ok?OK."Found":WRN."Unable to find")." SQL Patch [<b>".$sets["distsql"]."</b>]");
if(!$ok)
	$sets["distsql"] = "";

$sets["distdif"] = $sets["dist"]."patches/patch-".$sets["appver"].".diff";
$ok = is_file($sets["distdif"]);
if ($mode != "apply" && $mode != "confirm")
	i_verbose(($ok?OK."Found":WRN."Unable to find")." DIFF Patch [<b>".$sets["distdif"]."</b>]");
if(!$ok)
	$sets["distdif"] = "";

if(empty($sets["distsql"]) && empty($sets["distdif"]))
{
	i_verbose(" ");
	list($b1,$b2) = array($sets["incl"],$sets["excl"]);
	$sets["excl"] = array();
	$sets["incl"] = array("patch\-.*\.diff");
	$pvers = i_scan_tree($sets["dist"]."patches/", 4+8+32+64+128); # excl-filter + sorted + no-header&footer + logging
	if(empty($pvers))
	{
		$sets["incl"] = array("patch\-.*\.sql");
		$pvers = i_scan_tree($sets["dist"]."patches/", 4+8+32+64+128); # excl-filter + sorted + no-header&footer + logging
	}
	list($sets["incl"],$sets["excl"]) = array($b1,$b2);

	if($pvers)
	{
		i_verbose(NOK."Unable to find the matched version.");
		i_verbose("We do not have a patch file for the version of X-Cart you are using. You can attempt an install with one of the versions below, you should use whichever version is closest to your own.<br /><br /><strong>Please note:</strong> This is not guaranteed to install correctly, alternatively you could use the manual install method as detailed in the readme file or order our <a href=\"".$applications[$sets["app"]]['installservice']."\" target=_blank>install service</a>.",false);
		i_verbose(" ",false);

		$txt = NTG."Please select one from the list below:<br/>\n
            <form name='apply' method='get'>
            <input type='hidden' name='target' value='installmod' />";

		$rcmd = array();
		$df = $cv = i_version2int($av = $sets["appver"]);
		foreach($pvers as $pver)
		if(preg_match("/patch-(.*)\.(diff|sql)/i",$pver["name"],$o))
		{
			$tmp = abs($cv-i_version2int($o[1]));
			$rcmd[$o[1]] = $tmp;
			if($tmp<$df)
			{
				$av = $o[1];
				$df = $tmp;
			}
		}


		foreach($rcmd as $pver => $df_)
			$txt .= "<input type='radio' name='appver' value='".$pver."' id='appver".$pver."'".($pver==$av?" checked":"").">"."<label for='appver".$pver."'>Patch for ".$applications[$sets["app"]]['name']." ".$pver.($df==$df_?" <i>(closest match)</i>":"")."</label><br />\n";

		$txt .= "</form>";
		$txt .= '<div><ul id="page_actions" style="float:none;"><li class="single without_subitems"><a href="javascript:document.apply.submit();"><span style="width:auto;">Accept risk and apply alternative version</span></a></li><li class="single without_subitems"><a href="'.$applications[$sets["app"]]['installservice'].'" target="_blank"><span style="width:auto;">Our install service</span></a></li></ul></div>';
		i_exit($txt);
	}
	else
	{
		i_verbose(NOK."Sorry, unable to find any patch files in the patch folder [<b>".$sets["dist"]."patches/</b>]");
		i_verbose("Please be sure you upload a distributive correct!");
		i_exit(NTG.'<div><ul id="page_actions" style="float:none;"><li class="single without_subitems"><a href="'.TARGET_URL.'"><span>Check again</span></a></li></ul></div>');
	}
}




if($sets["distsql"])
{
	i_db_connect($sets["sql_host"], $sets["sql_user"], $sets["sql_password"], $sets["sql_db"]);

	if ($mode != "apply" && $mode != "confirm")
		i_verbose((mysql_errno()?NOK."Unable to establish":OK."Established")." MySQL connection [<b>".$sets["sql_user"]."@".$sets["sql_host"]."/".$sets["sql_db"]."</b>]");
	if(mysql_errno())
	{
		i_verbose("Please be sure the MySQL credentials are correct");
		i_exit(mysql_error());
	}

	if ($mode != "apply") i_verbose(" ");

	$log = array();
	if ($mode != "apply" && $mode != "confirm")
		i_verbose("Checking MySQL operations...");

	if($sets["skipmysqlcheck"] || $mode == "apply" || $mode == "confirm") {
		if ($mode != "apply" && $mode != "confirm")
		i_verbose(OK."Skipped");
	} else
	{
	$t = i_query("drop table if exists iscript_test",1); $t = $t[0];
	$op = "MySQL drop-if-exists: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("create table iscript_test (acolumn int(11) not null default 0)",1); $t = $t[0];
	$op = "MySQL create-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("alter table iscript_test add bcolumn int(11) not null default 0",1); $t = $t[0];
	$op = "MySQL alter-add-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("alter table iscript_test drop bcolumn",1); $t = $t[0];
	$op = "MySQL alter-drop-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("alter table iscript_test add index (acolumn)",1); $t = $t[0];
	$op = "MySQL alter-add-index: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("alter table iscript_test drop index acolumn",1); $t = $t[0];
	$op = "MySQL alter-drop-index: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("insert into iscript_test set acolumn=1",1); $t = $t[0];
	$op = "MySQL insert-into-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("delete from iscript_test where acolumn=2",1); $t = $t[0];
	$op = "MySQL delete-from-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	$t = i_query("drop table iscript_test",1); $t = $t[0];
	$op = "MySQL drop-table: ".($t["code"] ? $t["error"] : OK);
	i_verbose(".",$op);
	if($t["code"]) $log[] = $op;

	if(empty($log)) {
		if ($mode != "apply" && $mode != "confirm")
			i_verbose(OK."Done");
	} else
	{
		i_verbose(join("\n",$log),false);
		i_exit(NOK."Please grant the necessery MySQL permissions");
	}
	}
	i_verbose(" ");
}


/*********************** step 2 ***********************/
$new_ok = $dif_ok = 1;
$new_cmds = $dif_cmds = array();

if($sets["distnew"] && $mode != "apply")
{
	$sets["silentverbose"] = $mode == "confirm";

	i_verbose("Looking for new files...", false);
	#recur = 1; extra = 2; excld = 4; sort = 8; dirs_only = 16; silent = 32; noprogress = 64; logging = 128;
	$tree = i_scan_tree($sets["distnew"],1+2+32); # noheader+no
	i_verbose(OK."Done", false);

	list($new_ok,$new_cmds) = i_copy_tree($sets["distnew"],$sets["src"],$tree, 4+128); # 4: check; 128: logging
	if(!$new_ok && $sets["skipwwwcopy"])
		$new_cmds = array("cp -rf '".$sets["distnew"]."' '".$sets["src"]."'");

	i_verbose(" ");
	$sets["silentverbose"] = 0;
}

# prepare tree for backup
# copy files, what are in the system already, to the backup folder
if($tree)
{
	foreach($tree as $ti => $tk)
	if(!($tk["type"]=="F" && file_exists($sets["src"].$ti)))
		unset($tree[$ti]);
}
else
	$tree = array();

if($sets["distdif"] && $mode != "apply")
{

	$sets["silentverbose"] = $mode == "confirm";

	list($dif_ok,$dif_cmds,$list) = i_patcher($sets["src"],$sets["distdif"],1,0);

	if(!$dif_ok && empty($dif_cmds))
	{
		if($list)
		foreach($list as $tk)
		if(!$tk[0])
			i_verbose(NOK.$tk[1]." [<b>".$tk[2]."</b>]", false);
		i_exit("Sorry, unable to do the next step in automatic mode. Please use the manual install method or order our <a href=\"".$applications[$sets["app"]]['installservice']."\" target=_blank>install service</a>.");
	}

	if($dif_ok && $list)
	foreach($list as $tk)
	if(file_exists($sets["src"].$tk[2]))
		$tree[$tk[2]] = array(
			"path" => $tk[2],
			"type" => "F",
		);

	i_verbose(" ");

	$sets["silentverbose"] = 0;
}

# check permissions
// if(!$tree && $mode != "apply" && $mode != "confirm")
//	i_exit("No files to chmod - no files to work with...");

if(!$new_ok || !$dif_ok)
{
	i_verbose("Please run the following commands to move to the next step");
	i_verbose("<i>".join("<br>",array_merge($new_cmds,$dif_cmds)));
	i_exit();
}
/*********************** step 3 ***********************/

array_unshift($tree,array("path" => "", "type" => "D"));
# restore full tree for backup procedure
foreach($tree as $t_)
{
	$t_ = explode("\/",$t_["path"]);
	array_pop($t_);

	$ct = "";
	if($t_)
	foreach($t_ as $t__)
	{
		$ct .= $t__."/";
		if(!$tree[$ct])
			$tree[$ct] = array(
				"path" => $ct,
				"file" => $t__,
				"type" => "D",
				);
	}
}
usort($tree,"i_tree_cmp");



# restore security permission after installation
if($mode=="confirm")
{
	$ok = 1; $log = array();
	foreach($tree as $f)
	{
		$sh = $sets["src"].$f["path"];
/*		if(is_writable($sh))
		{
			$ok = 0;
			$log[] = "chmod ".($f["type"]=="F" ? 644 : 755)." '".$sh."'";
		} */
                if(is_writable($sh))
                {
                        $ok = 0;
                        $log[] = $sh;
                }
	}

	$ug_page = "<a href=\"".$applications[$sets["app"]]['userguidespages']."\" target='_blank' \">here</a>";

	if($ok)
	{
		i_verbose(OK."Permissions are fine");
		i_verbose(" ");
		i_verbose(OK."Installation is complete, you may wish to review the user guide for this addon $ug_page");
	}
	else
	{
		i_verbose(OK."Installation is complete, you may wish to review the user guide for this addon $ug_page");
		i_verbose(" ");
		i_verbose(EXC."<strong>Note:</strong> A list of new and updated files is shown below, in some circumstances their permissions may be changed during the install process.  Please check their permissions to confirm they are set to the appropriate level.  The permission level can differ depending on your server configuration<br /><br />\n<i>".join("<br />\n",$log)."</i>");

		#i_exit(NTG.'<div><ul id="page_actions" style="float:none;"><li class="single without_subitems"><a href="'.TARGET_URL."&mode=confirm".($appver?"&appver=".$appver:"").'"><span>Check again</span></a></li></ul></div>');

		#i_exit("<a href='".TARGET_URL."&mode=confirm".($appver?"&appver=".$appver:"")."'>Check again</a>");
	}

       file_put_contents($sets["dist"].'INSTALLED','Installed on '.date('Y-m-d H:i:s O')); 

        i_verbose(NTG.'<div align="center"><ul id="page_actions" style="float:none;" ><li class="single without_subitems"><a href="'.$_SERVER['REDIRECT_URL'].'?target=configuration" target="_parent"><span>See all addons</span></a></li></ul></div>');
        i_exit(NTG." ");
}
elseif($mode!="apply")
{
	i_exit(NTG.'<div><div width="40%" align="center"><ul id="page_actions" style="float:none;">
        <li class="single without_subitems">
        <a href="'.TARGET_URL.'&mode=apply'.($appver?"&appver=".$appver:"").'"><span>Apply the mod</span></a></li>
            </ul></div>'.($appver?'<div width="40%" align="right"><a href="'.TARGET_URL.'">Back to the version selection</a></div>':'').'</div>');
	#i_exit("<a href='".TARGET_URL."&mode=apply".($appver?"&appver=".$appver:"")."'>Apply mod?</a>".($appver ? " or <a href='".TARGET_URL."'>Back to the version selection</a>" : ""));
	#<a href='".TARGET_URL.($appver?"&appver=".$appver:"")."'>Check again</a>"
}

i_verbose(OK."Start applying...");
i_verbose(" ");

/*********************** step 3 ***********************/

i_verbose("Making a backup [<b>".$sets["backup"]."</b>]...");
$t = i_copy_tree($sets["src"],$sets["backup"],$tree, 1+2+128); # 1: overwr; 2: 644 perm; 4: check
if(!$t[0])
	i_exit(join("<br>\n",$t[1]));

i_verbose(" ");


if($sets["distsql"])
{
	$oksql = array(1050,1062,1061,1060);
	$ok = 1;
	$line = 0;
	$errsql = array();
	i_verbose("SQL Patch applying...");
	$fsql = fopen($sets["distsql"],"rt");
	while($s = fgets($fsql,100*1024))
	{
		$line++;
		i_verbose(".",false);
		$s = trim($s);
		$t = i_query($s,1); $t = $t[0];
		# table exists, duplicate for unique/primary, duplicate index, duplicate column
		$w = in_array($t["code"],$oksql);
		if($t["code"] && !$w)
			$ok = 0;

		$op = (!$ok?NOK:($w?WRN:OK))."SQL#".$line.": ".($t["code"] ? ($w ? "OK, but " : "").$t["error"] : "OK");
		i_verbose("",$op);
		i_verbose("",$s);
		if(!$ok)
			$errsql[] = $op;
		if(count($errsql)==10) # nah, that's enough to stop
		{
			$errsql[9] = NOK."and more...";
			break;
		}
	}
	fclose($fsql);
	if($ok)
		i_verbose(OK."Done");
	else
	{
		foreach($errsql as $op)
			i_verbose($op,false);
		i_exit(NOK);
	}

	i_verbose(" ");
}


if($sets["distnew"])
{
	$t = i_copy_tree($sets["distnew"],$sets["src"],"", 1+2+128); # 1: overwr; 2: 644 perm; 4: check;;; autogenerated tree
	if(!$t[0])
	{
		foreach($t[1] as $l)
			i_verbose(NOK.$l);
		i_exit(NOK."i_copy_tree/apply");
	}

	i_verbose(" ");
}

if($sets["distdif"])
{
	list($dif_ok,$dif_cmds,$list) = i_patcher($sets["src"],$sets["distdif"],0,0); # real apply
	if(!$dif_ok)
	{
		if($list)
		foreach($list as $tk)
		if(!$tk[0])
			i_verbose(NOK.$tk[1]." [<b>".$tk[2]."</b>]", false);

		i_exit(NOK."i_patcher/apply");
	}
}

i_exit(NTG.'<div align="center"><ul id="page_actions" style="float:none;" ><li class="single without_subitems"><a href="'.TARGET_URL."&mode=confirm".($appver?"&appver=".$appver:"").'"><span>Finalize</span></a></li></ul></div>');
#i_exit("<a href='".TARGET_URL."&mode=confirm".($appver?"&appver=".$appver:"")."'>Finalize</a>");


#############################################################################
# functions here...
#############################################################################

function i_db_connect($sql_host, $sql_user, $sql_password, $sql_db)
{
	$t = @mysql_connect($sql_host, $sql_user, $sql_password);
	return $t ? @mysql_select_db($sql_db) : false;
}

function i_db_query($query,$error=false)
{
	if(!($res = mysql_query($query)))
	{
		if($error)
			$res = -1;
		else
			print "<font style=\"color:red;\"><b>SQL error</b>: ".mysql_error()."</font><br>";
	}
	return $res;
}

function i_db_insert_id()
{	return mysql_insert_id();}

function i_query($query,$error=false)
{
	$result = array();
	if($res = i_db_query($query,$error))
	if(is_resource($res))
	{
		while($arr = mysql_fetch_array($res,MYSQL_ASSOC))$result[]=$arr;
		mysql_free_result($res);
	}
	elseif($res==-1 && $error)
		$result[] = array("code"=>mysql_errno(),"error"=>mysql_error());

	return $result;
}

function i_query_first($query)
{
	$arr = i_query($query);
	if($arr) return array_shift($arr);
		else return array();
}

function i_query_scalar($query)
{
	$arr = i_query($query);
	if($arr) return array_shift(array_shift($arr));
		else return "";
}

function i_show_size($size)
{
	if($size>1048576)
		{ $size/=1048576;$fm = "%.2f Mb";}
	elseif($size>1024)
		{ $size/=1024; $fm = "%.2f Kb";}
	else $fm = "%d b";
	return sprintf($fm,$size);
}

function i_include($file,$excl)
{
	global $sets;

	if(preg_match("/\.{1,2}\/$/",$file))
		return 0;

	$res = 1;
	if($sets["excl"] && $excl)
	foreach($sets["excl"] as $v)
	if(preg_match("/".$v."/i",$file))
		{$res = 0; break;}

	if(preg_match("/\/$/",$file))
		return $res;

	if($res && $sets["incl"] && $excl)
	foreach($sets["incl"] as $v)
	{
		if(preg_match("/".$v."/i",$file,$out))
		{
			$res = 1;
			break;
		}
		else
			$res = 0;
	}

	return $res;
}

function i_drop_tree($dir,$dropme=0)
{
	$ok = 1; $log = array();

	$dir = i_norm_dir($dir);

	i_verbose("<b>".$dir."</b> is dropping...");
	if(is_dir($dir))
	{
	$dirs = i_scan_tree($dir,1+8+16); #recur+sort+dirs
	if($dirs)
	{
		$dirs = array_reverse($dirs);
		foreach($dirs as $dir_)
		{
			$tmpdir = $dir.$dir_["path"];
			$files = i_scan_tree($tmpdir,0);
			if($files)
			foreach($files as $file)
			{
				i_verbose(".");
				$fname = $tmpdir.$file["path"];
				if($file["type"]=="F")
				{
					$a = @unlink($fname);
					if(!$a)
					{ $ok = 0; $log[] = "Can't delete file [".$fname."]";}
				}
			}
			if($dir_["path"] || $dropme)
			{
				$a = @rmdir($tmpdir);
				if(!$a)
					{ $ok = 0; $log[] = "Can't delete dir [".$tmpdir."]";}
			}
		}
	}
	}
	else
	{ $ok = 0; $log[] = "Dir isn't exist [".$dir."]";}

	if($ok)
	{
		$log[] = "Checkpoint has been dropped [".$dir."]";
		i_verbose("<b>".$dir."</b> has been dropped");
	}
	return array($ok,$log);
}

function i_tree_cmp($a,$b)
{
	if($a["type"] == $b["type"])
		return ($a["path"]<$b["path"] ? -1 : 1);
	else
		return ($a["type"]<$b["type"]) ? -1 : 1;
}

function i_norm_dir($dir)
{
	if(!in_array(substr($dir,-1,1),array("",".","/")))$dir.="/";
	return $dir;
}

function i_scan_tree($dir=".",$mask=5,$int="")
{
	$recur = $mask & 1; #def
	$extra = $mask & 2;
	$excld = $mask & 4; #def
	$sort = $mask & 8;
	$dirs_only = $mask & 16;
	$silent = $mask & 32;
	$noprogress = $mask & 64;
	$logging = $mask & 128;

	if(!$int)$dir = i_norm_dir($dir);

	$op = "Scaning <b>".($int ? ".../".$int : $dir)."</b> for ".($dirs_only ? "dirs" : "files")."...";
	i_verbose($silent?"":$op, $logging?$op:false);

	$_tree = $int && !$dirs_only ? array() : array(""=>array("path"=>"","name"=>"","type"=>"D"));

	if(is_dir($dir.$int))
	if($handle = opendir($dir.$int))
	{
	while (($file = readdir($handle)))
	{
		$full = $int.$file;
		$is_dir = is_dir($full_ = $dir.$full);
		if($is_dir) { $full.="/"; $tmp = $full;} else $tmp = $file;
		if(i_include($tmp,$excld))
	{
		i_verbose($noprogress?"":".", $logging?" (".($is_dir?"D":"F").") ".$file:false);
		if($is_dir)
		{
			if($extra)
				$_tree[$full] = array("path"=>$full,"name"=>$file,"type"=>"D");
			else
				$_tree[] = array("path"=>$full,"name"=>$file,"type"=>"D");

			if($recur)
				$_tree = array_merge($_tree,i_scan_tree($dir,$mask,$full));
		}
		elseif(!$dirs_only)
		{
			if($extra)
				$_tree[$full] = array("path"=>$full,"name"=>$file,"type"=>"F","extra"=>array("size"=>filesize($full_),"mtime"=>filemtime($full_)));
			else
				$_tree[] = array("path"=>$full,"name"=>$file,"type"=>"F"); 
		}

	}
	}
    	closedir($handle);
	}

	if(!$int)
	{
		$op = "Scaning is done";
		i_verbose($silent?"":$op, $logging?$op:false);
		if($sort)
			usort($_tree,"i_tree_cmp");
	}
	return $_tree;
}

function i_copy_tree($src,$dst,$tree, $mask=0) #, $scanmask = 1+4+16)
{
	$cmds = array();

	$rewrt = $mask & 1; #def is off
	$chkf = $mask & 4; #def is off
	$logging = $mask & 128; 

	$src = i_norm_dir($src);
	$dst = i_norm_dir($dst);

	i_verbose(($chkf ? "Checking files permissions to copy" : "Copying")." files from <b>".$src."</b> to <b>".$dst."</b>...", $logging?"":false); #verbose

	if(!is_dir($src))
		i_exit(NOK."i_copy_tree/".($chkf?"CHK":"WRK").":Source dir does not exist [<b>".$src."</b>]");

	if(!is_dir($dst))
		i_exit(NOK."i_copy_tree/".($chkf?"CHK":"WRK").":Destinatiin dir does not exist [<b>".$dst."</b>]");

	#recur = 1; extra = 2; excld = 4; sort = 8; dirs_only = 16; silent = 32; noprogress = 64; logging = 128;
	if(!$tree)
		$dirs = i_scan_tree($src, 1+4+8+16+32);
	else
		$dirs = array(-1);

	$dircache = array(
		$dst => i_make_dir($dst,$chkf)
	);
	if($dirs)
	foreach($dirs as $dir)
	{
		if($dir==-1)
		{
			$dst_ = $dst;
			$src_ = $src;
		}
		else
		{
			$tree = i_scan_tree($src.$dir["path"],4+8+32+64);
			$dst_ = $dst.$dir["path"];
			$src_ = $src.$dir["path"];
		}
	/*************************************************************/
	foreach($tree as $file)
	{
		$sh = $dst_.$file["path"];
		$psh = i_norm_dir(dirname($sh));
		i_verbose(".",false);

		if($file["type"]=="D")
		{
			$dircache[$sh] = i_make_dir($sh,$chkf);
			if($logging)
				i_verbose("", ($chkf?($dircache[$sh][0]==1?WRN:OK):($dircache[$sh][0]==2?WRN:($dircache[$sh][0]==1?NOK:OK)))." (D) ".$sh.": ".($dircache[$sh][0]==2?"OK, but permissions":($dircache[$sh][0]==1?"Does not exist":"OK")));

			# if current.dir does not exist, we need to chmod parent folder
			if($chkf)
			if($dircache[$sh][0]==1 && $dircache[$psh][0]==2 && !$dircache[$psh]["incl"])
			{
				$cmds = array_merge($cmds,$dircache[$psh][1]); #,$dircache[$sh][1]);
				$dircache[$psh]["incl"] = 1;
				i_verbose("", WRN." --- Asked for permissions on ".$psh);
			}
		}
		elseif($file["type"]=="F" && $chkf)
		{
			if(file_exists($sh))
			{
				$wr = is_writable($sh);
				$op = " (F) ".$sh.": On the place".($wr?"":", but permissions");
				$sz = filesize($sh);
				if($sz!=$file["extra"]["size"])
				{
					$op.= " plus the size is ".abs($sz-$file["extra"]["size"])." bytes ".($sz>$file["extra"]["size"]?"less":"more");
					$op = WRN.$op;
					if(!$wr)
						$cmds[] = "chmod 755 '".$sh."'";
				}
				else
					$op = OK.$op;
			}
			else
			{
				# if current.dir is not writable, and we have file(s) in it...
				if($dircache[$psh][0]==2 && !$dircache[$psh]["incl"])
				{
					$cmds = array_merge($cmds,$dircache[$psh][1]);
					$dircache[$psh]["incl"] = 1;
					i_verbose("", WRN." --- Asked for permissions on ".$psh);
				}

				$op = OK." (F) ".$sh.": Will be copied";
			}

			if($logging)
				i_verbose("", $op);
		}
		elseif($file["type"]=="F") # && ($rewrt || (!$rewrt && !file_exists($sh))))
		{
			i_verbose("","Copy ".$src_.$file["path"]." => ".$sh);
			if(file_exists($sh) && filesize($sh)==filesize($src_.$file["path"]))
				i_verbose("",OK." -- Skipped");
			else
			{
				$res = @copy($src_.$file["path"],$sh);
				@chmod($sh,0666);
				if(!$res)
				{
					$ok = 0;
					$cmds[] = "Can't copy [".$src_.$file["path"]."] to [".$sh."]";
					i_verbose("",NOK." -- Unable to copy");
				}
			}
		}
	}
	/*************************************************************/
	}

	i_verbose((empty($cmds)?OK:NOK).($chkf ? "Checked" : "Copied").(empty($cmds)?"":", but errors"), $logging?"":false);

	return array(empty($cmds),$cmds);
}

function i_array_map($func, $var) {
        if (!is_array($var)) return $var;

        foreach($var as $k=>$v)
                $var[$k] = call_user_func($func,$v);

        return $var;
}

function i_addslashes($var) {
        return is_array($var) ? i_array_map('i_addslashes', $var) : addslashes($var);
}


function i_redirect($mode)
{
	print "<META http-equiv=\"Refresh\" content=\"1;URL=".TARGET_URL."&mode=".$mode."\">";
	print "<p><a href=\"".TARGET_URL."&mode=".$mode."\">next step...</a>";
	exit;
}

function i_color_diff($diff)
{
	if(empty($diff))
		return "no diff";

	$colors = array("+"=>2, "-"=>1);

	$lines = explode("\n",$diff);
	$res = "<table border=0 cellpadding=2 cellspacing=0 width=100%>";
	foreach($lines as $line)
	{
		preg_match("/^(.)(.*)$/",$line,$fs);
		if(!$fs[2])$fs[2]=" ";elseif(!in_array($fs[1],array(" ","+","-")))$fs[2]=$line;
		$res.= "<tr class=diff".$colors[$fs[1]]."><td><tt>".htmlentities($fs[2])."</td></tr>";
	}
	$res.= "</table>";
	return $res;
}

function i_exit($line_="")
{
	global $sets;

	i_verbose(" ");
?>
<script>s = document.getElementById('support_');if(s)s.style.display = '';</script>
<?php
	$type = "";
	if($line_!==false && $line_!=="")
	{
		list($line_,$type) = i_type($line_);
		$type .= "!";
	}

	$line = $line_ ? $line_ : false; #"Exit.";
	i_verbose($type.$line, $line_===false ? false : $type.$line." (".date("H:i:s d.m.Y").")");
?>
</body>
</html>
<?php
	exit;
}

function i_type($line)
{
	if($line==" ")
		return array("","");
	if(preg_match("/^([A-Z]{3})\!(.*)$/iUs",$line,$l))
		list(,$type,$line) = $l;
	else
		$type = "NFO";
	return array($line,$type);
}

function i_verbose($line,$line_="")
{
	global $sets, $types;

	if ($sets["silentverbose"]) return;

	if($line==" ")
	{
		if($sets["islasthr"])
			return;
		$sets["islasthr"] = 1;
	}
	elseif($line)
		$sets["islasthr"] = 0;

	if(!$sets["expert"] && $line)
	{
		if($line==".")
		{
			if(($sets["verbose_cnt"]++)==150)
				$sets["verbose_cnt"]=0;

			print "<script>pl('".($sets["verbose_cnt"] ? "" : "<br>").".');</script>\n";
		}
		else
		{
			$sets["verbose_cnt"]=0;
			list($line,$type) = i_type($line);

			print "<script>pl('".($type&&$sets["start_time"]?"<br>":"").$types[$type][1].strtr($line,array("\n"=>"","\r"=>"","\\"=>"/","'"=>"\\'"))."');</script>\n";
		}

		flush();
	}

	if(!$sets["start_time"])
		$sets["start_time"] = microtime();

	if($line_!==false)
	{
		list($line_,$type) = i_type($line_?$line_:$line);

		if($sets["expert"])
		{
			if($type)
			print "<script>pl('<div><div class=\"sts ".$types[$type][2]."\">[".$types[$type][0]."]</div> ".strtr($line_,array("\n"=>"","\r"=>"","\\"=>"/","'"=>"\\'"))."</div>');</script>\n"; # js inside
			else
			print "<script>pl('".$types[$type][1]."');</script>\n";
			flush();
		}

		$log = fopen($sets["logfile"],"at");
		fputs($log, "[".i_timeframe($sets["start_time"],microtime(),1)."|".$types[$type][0]."] ".strip_tags($line_)."\n");
		fclose($log);
	}
}

function i_timeframe($st,$et,$raw=0)
{
	list($a,$b) = explode(" ",$st);$st = $b ? $a+$b : $a;
	list($a,$b) = explode(" ",$et);$et = $b ? $a+$b : $a;
	$v = abs($et-$st);
	if($raw)
		return sprintf("%8.4f",$v);
	elseif($v<60)
		return sprintf("%.4f sec",$v);
	elseif($v<3600)
		return sprintf("%d min %d sec",floor($v/60),($v%60));
	elseif($v<24*3600)
		return sprintf("%d h %d min",floor($v/3600),floor(($v%3600)/60));
	else
		return sprintf("%d day(s) %d h",floor($v/86400),floor(($v%86400)/3600));
}

function i_make_dir($dir,$chk=0)
{
	$ok = 0;
	$cmds = array();

	if(!is_dir($dir))
	{
		$res = $chk ? 0 : @mkdir($dir,0755);
		if(!$res)
		{
			$ok += 1; # can't be created
			$cmds[] = "mkdir '".$dir."'";
			if($chk)
				$cmds[] = "chmod 755 '".$dir."'"; # if we're checking, need to suggest change the permission additionally
		}
	}

	if(is_dir($dir))
	{
		if(!$chk)
			@chmod($dir,0755);
		if(!is_writable($dir))
		{
			$ok += 2; # Dir exists, but it is not writable
			$cmds[] = "chmod 755 '".$dir."'";
		}
	}

	return array($ok,$cmds);
}

function i_uniqnum()
{
	return substr(md5(uniqid(rand())),0,8);
}

function i_http_get($url) {

	$result = "";
	$header_passed = false;
	$url = parse_url($url);
	if(!$url["port"])
		$url["port"] = 80;

	$fp = fsockopen($url["host"], $url["port"], $errno, $errstr, 15);
	if ($fp) {
		fputs ($fp, "GET ".$url["path"]."?".$url["query"]." HTTP/1.0\r\n");
		fputs ($fp, "Host: ".$url["host"]."\r\n");
		fputs ($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
		fputs ($fp,"\r\n");

		$http_header = array ();
		$http_header["ERROR"] = chop(fgets($fp,4096));

		while (!feof($fp)) {
			if (!$header_passed)
				$line = fgets($fp, 4096);
			else
				$result .= fread($fp, 65536);

			if ($header_passed == false && ($line == "\n" || $line == "\r\n")) {
				$header_passed = true;
				continue;
			}
		}

		fclose($fp);
	}

	return array($http_header,$result);
}

function i_patcher($src,$difile,$check=0,$reverse=0)
{
	$arr = array("10"=>"Testing patch", "11"=>"Testing rollback", "00"=>"Applying patch", "01"=>"Applying rollback" );
	$rev = array("+" => "-", "-" => "+", " " => " ");

	i_verbose($arr[$check.$reverse]." for <b>".$difile."</b>...");

	$patch = "";
	$diff_ = $hunk_ = 0;
	$cmds = $log = $hunk = $hunks = $hunkh = array();

	$fdiff = fopen($difile,"rt");
	do
	{
		$line = fgets($fdiff,5*1024);
		$end_ = feof($fdiff);

		if(!$end_)
			preg_match('/^((index): (.+))|((\+{3}|\-{3}) ([^\t\n:]+))/i',$line, $out);
		if(!$diff_)
			$diff_ = strtolower($out[2] ? $out[2] : "---");
		if($diff_=="---" && $out[5]=="+++" && $out[6])
			$patch = trim($out[6]);

		if($end_ || $diff_==strtolower($out[2].$out[5]))
		{
			if($hunk)
			{
				$hunkh[0] = $hunk;
				$hunks[] = $hunkh;
				$log[] = i_patcher_file($check,$src,$patch,$hunks);
			}

			if($end_)
				break;

			$hunk = "";
			$hunk_ = 0;
			$hunks = array();
			$patch = trim($out[3] ? $out[3] : $out[6]);
		}
		elseif(preg_match("/^@@ -(\d+)(?:,(\d+))? \+(\d+)(?:,(\d+))? @@/",$line,$h))
		{
			if($hunk)
			{
				$hunkh[0] = $hunk;
				$hunks[] = $hunkh;
			}

			$hunk = array();
			$hunk_ = $h[1]>0 && $h[2]>0 ? 1 : 0; # no hunk if it's a new file
			if($reverse)
			{
				$z = $h;
				list($h[0],$h[3],$h[4],$h[1],$h[2]) = $z;
				unset($z);
			}
			$hunkh = $h;
		}
		elseif($hunk_)
		if(preg_match("/^([\+\-\ ])/",$line))
			$hunk[] = $reverse ? $rev[substr($line,0,1)].substr($line,1) : $line;
	}
	while(!$end_);

	fclose($fdiff);

	$status = 0;
	$failed = 0;
	if($log)
	foreach($log as $k => $l)
	{
		$status += !$l[0]; # we're counting # of failed
		if($l[4])
			$cmds[] = $l[4];
		$failed |= count($l[5]);
	}

	global $mode, $sets;
	if ($failed) {
        	i_verbose(NTG."<br />", "");
		if ($mode != "apply") { 
			$warn_txt = EXC."The installation patch cannot be completely automatically applied to your store. If you continue with installation some files will not be completely patched and you will need to complete these code changes manually using the <a href='manuals/manual-".$sets["appver"].".htm' target='_blank'>step-by-step manual installation guide</a>";
		} else {
			$warn_txt = EXC."The patch has been applied to your store, some files could not be patched automatically and will need the code installed to manually.  These files are listed below, please consult the <a href='manuals/manual-".$sets["appver"].".htm' target='_blank'>step by step manual installation guide</a>";
		}

        	i_verbose($warn_txt, $warn_txt);
	
		if ($mode == "apply") {
			i_verbose(" ");
			$warn_txt = NTG."<b>Failed patches log:</b>";
			i_verbose($warn_txt, $warn_txt);
			foreach ($log as $k => $l) {
				if (!count($l[5])) continue;
				$err_text = "$l[2]";
				i_verbose($err_text);
				foreach ($l[3] as $hid => $l_msg) {
					i_verbose($l_msg);
				}
				i_verbose(" ");	
			}
		}
	}



	return array(!$status,$cmds,$log);
}

function i_patcher_file($check,$src,$file_,$hunks)
{

//	global $sets;

	$log = $a = $b = array();
	$cmd = "";
	$status = "Ok";
	$ok = $sk = 1;

	$file = $src.$file_;
	$hunks_ = count($hunks);

//	i_verbose(".","Found <b>".$file."</b>... ");

	i_verbose("Checking <b>".$file."</b>... ");

	if(!file_exists($file))
	{
		$status = "Not found";
		$ok = 0;
	}
	elseif(!is_file($file))
	{
		$status = "Not a file";
		$ok = 0;
	}
	else
	{
		$failed = array();
		$i_offset = $already_applied = 0;
		$outdata = file($file);
		foreach($hunks as $hunki => $hunk)
		{
			i_verbose(".",false);

			list($hunk,$i_start, $i_lines, $o_start, $o_lines) =  $hunk;

			$data = i_apply($outdata, $hunk, $i_start+$i_offset, $i_lines, $o_start, $o_lines);

			if (is_array($data)) {
				$hunk_pos = $i_start + $i_offset + ($data[1] === false ? 0 : $data[1]);
				if ($data[0]==1) {
					$log[] = OK." - ".sprintf("Hunk #%d succeeded at line %d.", $hunki, $hunk_pos);
					array_splice($outdata, $hunk_pos-1, $i_lines, $data[2]);
					$i_offset += $o_lines - $i_lines + $data[1];
				} elseif($data[0]==-1) {
					$log[] = OK." - ".sprintf("Hunk #%d already applied at line %d.", $hunki, $hunk_pos);
					$already_applied++;
				} else {
					$log[] = NOK." - ".sprintf("Hunk #%d failed at line %d.", $hunki, $hunk_pos);
					$i_offset += $o_lines - $i_lines;
					$failed[] = $hunki;
				}
			}
		}
		if(empty($failed))
		{
			if($already_applied == count($hunks))
			{
				$status = "Already patched";
			}
			elseif(!is_writable($file))
			{
				$ok = 0;
				$status = "Ok, but permissions";
				$cmd = "chmod 755 '".$file."'";
			}
			elseif(!$check)
			{
				$f = fopen($file,"wt");
				fputs($f, join($outdata));
				fclose($f);
			}
		}
		else {
//			$ok = $sets["skipfailedpatch"] ? 1 : 0;
			$ok = 1;
			$sk = 0;
			$status = ($ok?"Skippped.":"Error.")." Failed hunks #".join(", #",$failed);
		        
/*			foreach($log as $l)
                		i_verbose($l,$l);*/
/*
$warn_txt = NOK."Installation patch has conflicts with the store code. Most likely your the store has been modified and file(s) differ from default. Please proceed only if you understand the risk and accept possibility of negative consequences";

			i_verbose($warn_txt, 1);
*/
		}

	}

//	i_verbose("",(!$sk&&$ok?WRN:($ok?OK:NOK))."Summary for ".$hunks_." hunk".($hunks_==1 ? "" : "s").": ".$status);
/*
	if($log)
	foreach($log as $l)
		i_verbose($l, $l);
*/
i_verbose((count($failed)?NOK:OK)."Summary for ".$hunks_." hunk".($hunks_==1 ? "" : "s").": ".$status, 1);
//$sets["expert"] = $exp_sets;

	return array($ok,$status,$file_,$log,$cmd,$failed);
}

/*****************************************************************************************************************/

# Pass empty $outfile to check patch applicability
#

function i_apply(&$outdata, &$hunk, $i_start, $i_lines, $o_start, $o_lines)
{
	$offset = i_locate($outdata, $hunk, $i_start, $i_lines);

	$result = array (0, $offset);

	$roffset = i_locate($outdata, $hunk, $o_start, $o_lines, 1); # 1 -- chk reverse
	if ($offset === false && $roffset === false)
		return $result;
	elseif($roffset !== false)
		return array (-1, $roffset);

	$work_copy = array_slice($outdata,$i_start-1+$offset,$i_lines);
	$pos = 0;
	foreach ($hunk as $line) {
		if (strlen($line)>0) {
			$cmd = $line[0];
			$line = substr($line,1);
		}
		else $cmd = '';

		switch ($cmd) {
			case '-':
				$_line = trim($line);
				$_work_copy = trim($work_copy[$pos]);
				if ($_line != $_work_copy) {
					# FAILED
					return $result;
				}
				array_splice($work_copy,$pos,1);
				break;
			case '+':
				i_array_insert($work_copy,$line,$pos);
				$pos++;
				break;
			default :
				# skip ...
				$pos++;
		}
	}

	return array(1,$offset,$work_copy);
}

function i_array_insert(&$array, $value, $pos) {
	if (!is_array($array)) return FALSE;

	$last = array_splice($array, $pos);
	array_push($array, $value);
	$array = array_merge($array, $last);
	return $pos;
}

function i_locate(&$data, &$hunk, $start, $lines, $reverse=0) {
	$data_len = count($data);

	$max_after = $data_len - $start - $lines;
	for ($offset = 0; ; $offset++) {
		$check_after = ($offset <= $max_after);
		$check_before = ($offset <= $start);

		if ($check_after && i_match($data, $hunk, $start+$offset, $reverse)) {
			return $offset;
		}
		else
		if ($check_before && i_match($data, $hunk, $start-$offset, $reverse)) {
			return -$offset;
		}
		else
		if (!$check_after && !$check_before) {
			return false;
		}
	}

	return false;
}

function i_match(&$data, &$hunk, $pos, $reverse=0) {
	$len = count($hunk);
	$data_len = count($data);

	for ($i=0, $hunk_pos=0; $hunk_pos<$len && $pos+$i < $data_len; ) {
		if (!preg_match('!^(.)(.*)$!sS', $hunk[$hunk_pos], $matched)) {
			return false;
		}

		if ($matched[1] == ($reverse?'-':'+')) {
			$hunk_pos++;
			continue;
		}
		
		$_data = trim($data[$pos+$i-1]);
		$_match = trim($matched[2]);
		if ($_data != $_match) {
			return false;
		}

		$i++; $hunk_pos++;
	}

	return true;
}

function i_version2int($v) {

	$v = preg_replace("/[^\d\.]/","",$v);
	$v = explode(".",$v);
	$s = "";
	for($i=0;$i<5;$i++)
		$s .= $v[$i]>1000 ? "999" : sprintf("%03d",$v[$i]);
	return $s;
}

?>
