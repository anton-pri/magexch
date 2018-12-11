function ppd_popup_files (obj, name_prefix, path_prefix, images) {
	var filename = name_prefix + obj.id;
	var path = path_prefix + obj.id;

	var images_url_params = '';
	if (images) {
		images_url_params = '&tp=images';
	}

	var dir_url_params = '';
	if (ppd_dir) {
		dir_url_params = '&dir=' + ppd_dir;
	}

	window.open ("index.php?target=popup_files"+images_url_params+"&field_filename="+filename+"&field_path="+path+dir_url_params, "selectfile", "width=800,height=600,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
