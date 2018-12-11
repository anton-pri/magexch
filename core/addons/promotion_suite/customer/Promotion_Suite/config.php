<?php
// CartWorks.com - Promotion Suite

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
#x_load('debug');

// Allow multidomain support. MDM addon must be installed before.
define('PS_ALLOW_MDM',false);
define('PS_COND_LOGIC','OR'); // [AND|OR] conjunction of multiple categories, products, manufacturers conditions - shall be found all or any one

// Define if to check offers every time or use session cache.
// set to false in Production mode and to true in Dev mode
define('PS_FORCE_USER_BONUSES', true);

define('PS_VERSION', 'v.2.4.3 2012-10-22');

$addons['Promotion_Suite'] = PS_VERSION;

$css_files['Special_Offers'][] = array();

$tables["bonuses"]				= "xcart_bonuses";
$tables["bonuses_lng"]			= "xcart_bonuses_lng";
$tables["bonus_conditions"]	= "xcart_bonus_conditions";
$tables["bonus_supply"]		= "xcart_bonus_supply";
$tables["images_PS"]			= "xcart_images_PS";

# Multidomain addon support
if ((defined('PS_ALLOW_MDM') && constant('PS_ALLOW_MDM')==true) || (!empty($tables['domains']) && is_string($tables['domains']))) {
	$tables["domain_bonuses"]	= 'xcart_domain_bonuses';
}
$available_conditions = array('T','W','Z','C','M','P');
$available_supplies = array('D','C','P','S');

$config['available_images']['PS'] = "U";

include_once $xcart_dir.'/addons/Promotion_Suite/func.php';

// CartWorks.com - Promotion Suite
?>
