function popup_image(type, id, max_x, max_y, title) {

	max_x = parseInt(max_x);
	max_y = parseInt(max_y);

	if (!max_x)
		max_x = 160;
	else
		max_x += 100;
	if (!max_y)
		max_y = 120;
	else
		max_y += 130;

	// if image more then screen size
	if (max_x > $(window).width()) {
		max_x = $(window).width();
	}

	if (max_y > $(window).height()) {
		max_y = $(window).height();
	}

	return window.open(app_web_dir+'/index.php?target=popup_image&type='+type+'&id='+id+'&title='+title,'images','width='+max_x+',height='+max_y+',toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
}
