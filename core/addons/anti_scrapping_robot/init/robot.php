<?php
global $config, $app_main_dir, $smarty, $HTTP_USER_AGENT, $CLIENT_IP;

$browser = &cw_session_register("browser", array());
$is_robot = $browser['is_robot'];

if (!empty($config[anti_scrapping_addon_name]['scrapper_user_agent_likes'])) {
	$scrapper_user_agents = explode("\n", $config[anti_scrapping_addon_name]['scrapper_user_agent_likes']);
	$user_agent = strtolower($HTTP_USER_AGENT);

	if (is_array($scrapper_user_agents)) {
		foreach ($scrapper_user_agents as $value) {
			if (strpos($user_agent, strtolower(trim($value))) !== FALSE) {
				define("IS_ANTISCRAPE_ROBOT", TRUE);
				break;
			}
		}
	}
}

if (!empty($config[anti_scrapping_addon_name]['scrapper_ips']) && !defined("IS_ANTISCRAPE_ROBOT")) {
	$scrapper_ips = explode("\n", $config[anti_scrapping_addon_name]['scrapper_ips']);

	if (!empty($CLIENT_IP)) {
		foreach ($scrapper_ips as $value) {
			if ($CLIENT_IP == trim($value)) {
				define("IS_ANTISCRAPE_ROBOT", TRUE);
				break;
			}
		}
	}
}

if (
	empty($is_robot)
	&& !empty($HTTP_USER_AGENT)
	&& !defined("IS_ROBOT")
	&& defined("IS_ANTISCRAPE_ROBOT")
) {
	include $app_main_dir . '/include/lib/php-browser-detection.php';

	$browser = get_browser_info();
	$browser['is_robot'] = $is_robot = 'Other bots';
	define("IS_ROBOT", $is_robot);

	$smarty->assign('browser', $browser);
	$smarty->assign('is_robot', constant('IS_ROBOT'));
}
