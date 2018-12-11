<?php
if (!empty($_SERVER['REQUEST_METHOD'])) die();

if (!empty($addons['Maintenance_Agent']) && !empty($config['Maintenance_Agent']['periodic_type']) != '' && $config['Maintenance_Agent']['periodic_mode'] == 'M') {
	cw_send_periodical_email();
}

?>
