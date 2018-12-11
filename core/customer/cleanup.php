<?php
cw_load('files');
$type = trim($_GET['type']);
$result = cw_call('cw_cleanup_cache', array($type));

$messages = array(
	"" 		=> "cache and templates ",
	"tpl" 	=> "templates ",
	"cache" => "cache "
);
echo "Cleanup " . $messages[$type] . "is complete.";

if (!$result) {
	echo "<br /><b>Note:</b> Some files could not be removed. Please remove them yourself.";
}

echo "<br /> <a href='" . $current_location . "/index.php'>Home page</a>";

exit(0);
