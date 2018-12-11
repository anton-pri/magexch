<?php
namespace CW\wordpress;

$allowed_actions = array(
	'header',
	'footer',
);

if (!in_array($action,$allowed_actions)) {
	echo "element $action is not found";
	exit;
}

$content = cw_call(addon_namespace.'\get_'.$action);

echo $content;
exit;
