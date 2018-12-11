<?php
cw_include('include/lib/cache/CW_Cache.class.php');

global $cacheManager;
global $cw_cache_registry;

if (empty($var_dirs))
	$_cache_dir = $app_dir.'/var/cache/';
else
	$_cache_dir = $var_dirs['cache'] . '/';

$_cache_options = array(
    'cacheDir' => $_cache_dir,
    'lifeTime' => SECONDS_PER_WEEK,
    'automaticSerialization' => true,
    'fileNameProtection' => false,
    'hashedDirectoryLevel' => 0,
    'hashedDirectoryUmask' => 0777,
    'automaticCleaningFactor' => 50,
);

$cacheManager = new CW_Cache($_cache_options);

function cw_cache_get($id, $group='default') {
	global $cacheManager;
	if (defined('DISABLE_CACHE') && constant('DISABLE_CACHE')) return null;
	if (is_array($id)) $id = md5(serialize($id));
	return $cacheManager->get($id, $group);
}


function cw_cache_save($data, $id, $group='default') {
	global $cacheManager;
	if (defined('DISABLE_CACHE') && constant('DISABLE_CACHE')) return true;
	if (is_array($id)) $id = md5(serialize($id));
	return $cacheManager->save($data, $id, $group);
}

function cw_cache_clean($group) {
	global $cacheManager;
	if (defined('DISABLE_CACHE') && constant('DISABLE_CACHE')) return true;

	return $cacheManager->clean($group);
}

function cw_cache_register($group, $options) {
	global $cw_cache_registry;
	
	$cw_cache_registry[$group] = $options;
	
	return $options;
}
