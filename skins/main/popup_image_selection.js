/*
	Display popup window
*/
function popup_image_selection (type, id, imgid, tabs) {
	if ($('#image_dialog').length==0)
		$('body').append('<div id="image_dialog"></div>');
	// Load iframe with image selector into dialog
    $('#image_dialog').html("<iframe frameborder='no' width='828' height='325' src='index.php?target=image_selection&type="+type+"&id="+id+"&imgid="+imgid+"&tabs="+tabs+"'></iframe>");
    // Show dialog
    sm('image_dialog', 840, 365, true, 'Select image');
}

/*
	Reset new selected image
*/
function popup_image_selection_reset (type, id, imgid) {
	if (document.getElementById(imgid)) {
		var ts = new Date();
		document.getElementById(imgid).src = app_web_dir+"/index.php?target=image&type="+type+"&id="+id+"&ts="+ts.getTime();
		if (document.getElementById(imgid+'_text')) {
			document.getElementById(imgid+'_text').style.display = 'none';
			for (var cnt = 1; true; cnt++) {
				if (!document.getElementById(imgid+'_text'+cnt))
					break;
				window.opener.document.getElementById(imgid+'_text'+cnt).style.display = 'none';
			}
		}

		if (document.getElementById('skip_image_'+type))
			document.getElementById('skip_image_'+type).value = 'Y';
		else if (document.getElementById('skip_image_'+type+"_"+id))
			document.getElementById('skip_image_'+type+"_"+id).value = 'Y';

		if (document.getElementById(imgid+'_reset'))
			document.getElementById(imgid+'_reset').style.display = 'none';
	}
}

