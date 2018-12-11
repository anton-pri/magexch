<?php
$popup_users = &cw_session_register('popup_users');

if ($target_form) {
    $popup_users['form'] = $target_form;
    $popup_users['element_id'] = $element_id;
    $popup_users['user_type'] = $user_type;
}
$target_form = $popup_users['form'];
$element_id = $popup_users['element_id'];
$usertype = $popup_users['user_type']?$popup_users['user_type']:'C';

$format = "~~firstname~~ ~~lastname~~ (~~email~~)";

function cw_set_saved_users($users) {
	global $format, $form, $force_submit;
	$useids = array();
	foreach ($users as $u) {
		$useids[] = strtr($u['customer_id'], array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
	}
	$useids = implode(";", $useids);
?>
<html><body>
<script type="text/javascript">
<!--
if (window.opener && window.opener.document.<?php echo $form; ?> && window.opener.document.<?php echo $form; ?>.userids) {

	var f = window.opener.document.<?php echo $form; ?>;
	f.userids.value = '<?php echo $useids; ?>';

	if (f.users) {
		var isSelect = (f.users.tagName.toUpperCase() == 'SELECT');
		if (isSelect) {
			while (f.users.options.length > 0)
				f.users.options[0] = null;
		} else {
			f.users.value = '';
		}

		var i = 0;
		with (f.users) {
<?php
	foreach ($users as $u) {
		$str = $format;
		foreach ($u as $fn => $fv) {
			$str = str_replace("~~".$fn."~~", $fv, $str);
		}
		$str = strtr($str, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
		$l = strtr($u['customer_id'], array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
?>
			if (isSelect)
				options[i++] = new Option('<?php echo $str; ?>', '<?php echo $l; ?>');
			else
				value += (value.length == 0 ? "" : "\n") + "<?php echo $str; ?>";
<?php
	}

?>
		}
	}

	if (window.opener.document.getElementById('<?php echo $form; ?>_users_count'))
		window.opener.document.getElementById('<?php echo $form; ?>_users_count').innerHTML = '<?php echo count($users); ?>';

<?php
if ($force_submit) {
?>
	f.submit();
<?php
}
?>
}
-->
</script>
</body></html>
<?php
}

if ($action == "save") {
	if (!empty($user)) {
		$users = cw_query("SELECT * FROM $tables[customers] WHERE customer_id IN ('".implode("','", array_keys($user))."')");
		cw_set_saved_users($users);
		if ($force_submit)
			cw_close_window();
	}
	cw_close_window();
}

$search_script = 'index.php?target=popup_users&form='.$form.'&mode=search';
$objects_per_page = $config['Appearance']['users_per_page_admin'];

if ($current_area == 'B')
    $search_data['users'][$usertype]['sale']['sales_manager'][$customer_id] = 1;

include $app_main_dir.'/include/users/search.php';

$smarty->assign('target_form', $target_form);
$smarty->assign('element_id', $element_id);

$location[] = array(cw_get_langvar_by_name('lbl_popup_users'), '');
$location[] = array(cw_get_langvar_by_name('lbl_users_'.$usertype, ''));
?>
