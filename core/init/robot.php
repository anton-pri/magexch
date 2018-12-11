<?php
// Main robots definitions can be found at http://www.user-agents.org/index.shtml

$browser = &cw_session_register("browser", array());
$is_robot = $browser['is_robot'];

if (empty($is_robot) && !empty($HTTP_USER_AGENT) && !defined("IS_ROBOT")) {
// First detection
    $bots = array(
    'Google'    => array('Googlebot','gsa-crawler','Python-urllib'),
    'AOL'       => array('asterias','Sqworm'),
    'Yahoo'     => array('Yahoo'),
    'Yandex'    => array('Yandex'),
    'Altavista' => array('AltaVista','Scooter'),
    'Amazon'    => array('Twitturly','zermelo'),
    'Ask'       => array('Ask Jeeves'),
    'MSN'       => array('msnbot'),
    'Lycos'     => array('Lycos'),
    'DeepIndex' => array('DeepIndex'),
    'Other heritrix' => array('heritrix'),
    'Other bots'     => array('crawl','spider','robot','search'),
    );


    foreach ($bots as $b => $v) {
		foreach ($v as $a) {
			if (stripos($HTTP_USER_AGENT, $a) !== false) {
	    		define("IS_ROBOT", $b);
				break;
			}
		}
		if (defined("IS_ROBOT")) break;
	}

    $is_robot = defined("IS_ROBOT")?constant('IS_ROBOT'):'N';
    unset($bots,$v,$b,$a);

    include $app_main_dir.'/include/lib/php-browser-detection.php';

    $browser = get_browser_info();

} elseif (defined("IS_ROBOT")) {
// Robot already detected
	$is_robot = constant('IS_ROBOT');
} elseif (!empty($is_robot)) {
// Robot is in session
    if ($is_robot!='N')
    	define("IS_ROBOT", $is_robot);
}

$browser['is_robot'] = $is_robot;

$smarty->assign('browser', $browser);
if (defined('IS_ROBOT'))
    $smarty->assign('is_robot', constant('IS_ROBOT'));
