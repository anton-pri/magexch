function popup_magnifier(id, max_x, max_y, image_id) {

    max_x = parseInt(max_x);
    max_y = parseInt(max_y);

    if (!max_x)
        max_x = 530;

    if (!max_y)
        max_y = 600;

	if (!image_id)
		image_id = '';
		
    return window.open(app_web_dir+'/index.php?target=popup_magnifier&product_id='+id+'&image_id='+image_id, 'magnifier', 'width='+max_x+',height='+max_y+',toolbar=no,status=no,resizable=no,menubar=no,location=no,direction=no');
}
