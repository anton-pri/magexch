function cw_popup_user(form, element_id, user_type) {
	return window.open ("index.php?target=popup_users&target_form="+form+"&element_id="+element_id+"&user_type="+user_type, "select_user", "width=800,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
