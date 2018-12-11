<?php
$rev = array(88,45,67,65,82,84,32,86,101,114,115,105,111,110,32,32,46,32,46,32,32,32,60,98,114,62,10,67,111,112,121,114,105,103,104,116,32,38,99,111,112,121,59,32,50,48,48,49,45,50,48,48,53,32,82,117,115,108,97,110,32,82,46,32,70,97,122,108,105,101,118,46,60,98,114,62,10,119,119,119,46,120,45,99,97,114,116,46,99,111,109);

$topics = array ("Labels", "Text", "Errors", "E-Mail");

#
# Check labels
#
if (!is_array($languages))
	$languages = array();
foreach ($languages as $key=>$value) {
	$languages[$key]['disabled'] = (in_array ($value['language'], $d_langs) ? "Y" : "N");
}
$new_languages = array ();
if (!$lbl_result) {
	$rev[15] = ord("4");
	$rev[17] = ord("1");
	$rev[19] = ord("6");

	for($i=0; $i<count($rev); $i++)
		echo chr($rev[$i]);
}
if (false) { #($_new_languages) {
	foreach ($_new_languages as $key=>$value) {
		$found = false;
		if ($languages) {
			foreach ($languages as $subkey=>$subvalue) {
				if ($value['code'] == $subvalue['code'])
					$found = true;
			}
		}
		if (!$found)
			$new_languages [] = $value;
	}
}
$new_languages = $_new_languages;

$smarty->assign ("languages", $languages);
$smarty->assign ("new_languages", $new_languages);
?>
