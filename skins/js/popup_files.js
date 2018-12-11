function popup_files (filename, path) {
	window.open ("index.php?target=popup_files&field_filename="+filename+"&field_path="+path, "selectfile", "width=800,height=600,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}

function popup_images (filename, path) {
	window.open ("index.php?target=popup_files&tp=images&field_filename="+filename+"&field_path="+path, "selectfile", "width=800,height=600,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
