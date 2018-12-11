<?php

/**
 * @param $params
 * @param $return
 * @return mixed
 */
function cw_anti_scrapping_product_search($params, $return) {
	$percent = cw_anti_scrapping_get_percent();

	if (
		$return[0]
		&& !empty($percent)
		&& defined("IS_ANTISCRAPE_ROBOT")
	) {
		foreach ($return[0] as $k => $v) {
			$price = $return[0][$k]['price'];
			if (!empty($price)) {
				$return[0][$k]['price'] = $price + $price * $percent / 100;
			}
			$price = $return[0][$k]['list_price'];
			if (!empty($price)) {
				$return[0][$k]['list_price'] = $price + $price * $percent / 100;
			}
			$price = $return[0][$k]['display_price'];
			if (!empty($price)) {
				$return[0][$k]['display_price'] = $price + $price * $percent / 100;
			}
		}
	}

	return $return;
}

/**
 * @param $params
 * @param $return
 * @return mixed
 */
function cw_anti_scrapping_product_get($params, $return) {
	$percent = cw_anti_scrapping_get_percent();

	if (
		$return
		&& !empty($percent)
		&& defined("IS_ANTISCRAPE_ROBOT")
	) {
		$price = $return['price'];
		if (!empty($price)) {
			$return['price'] = $price + $price * $percent / 100;
		}
		$price = $return['display_price'];
		if (!empty($price)) {
			$return['display_price'] = $price + $price * $percent / 100;
		}
		$price = $return['taxed_price'];
		if (!empty($price)) {
			$return['taxed_price'] = $price + $price * $percent / 100;
		}
	}

	return $return;
}

/**
 * @return float
 */
function cw_anti_scrapping_get_percent() {
	global $config;

	$percent = $config[anti_scrapping_addon_name]['percent_for_price_change'];

	if (abs($percent) > 100) {
		$percent = $percent / abs($percent) * 100;
	}

	return $percent;
}
