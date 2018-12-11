<script type="text/javascript">
//<!--

var thumbsPosition = "{$config.detailed_product_images.dpi_viewer_thumbnails_position|default:'bottom'}";

{literal}
var imageGalleryOptions = {
	thumbs: 5, 		/* Amount of thumbnails displayed in the gallery, 5 */
	overlayColor: '#999', 		/* Color of the modal, # 999 */
	overlayOpacity: 0.8,	 	/* Opacity of modal (0.0 to 1.0), 0.8 */
	overlaySpeed: 500,			/* Speed of emergence and disappearance of modal time (in milliseconds), 500 */
	scrollSpeed: 500,			/* Speed paging galleries (time in milliseconds), 500 */
	zoomSpeed: 250,				/* Speed of the zooming effect of images (time in milliseconds), 250 */
	showOne: false,				/* Show only the first image on page (true / false), false */
	keyboardNavigation: false,	/* Toggle keyboard navigation (true / false), true */
	showLabel: true,			/* View a description of the image (true / false), true */
	labelPosition: 'bottom',	/* Position description of the image (bottom / top), bottom */
	dockPosition: thumbsPosition, 		/* Position of the gallery (top / bottom), top */
	maxThumbWidth: undefined,	/* Maximum width of a thumbnail in the gallery, to force your resizing (in pixels), undefined */
	maxThumbHeight: undefined, 	/* Maximum height of a thumbnail in the gallery, to force your resizing (in pixels), undefined */
	timeOut: 15,				/* TimeOut 	Maximum waiting time for loading the image until the thumbox abort the loading (in seconds), 15 */
	wheelNavigation: true,		/* Enable browsing with the "wheel" mouse * (true / false), true */
	openImageEffect: 'linear',	/* Effect when viewing the image, linear */
	closeImageEffect: 'linear',	/* Effect to close the picture, linear */
	scrollDockEffect: 'linear',	/* Effect when scrolling the dock, linear */
};

$(document).ready(function(){
	$('#dpi-thumbnails').thumbox(imageGalleryOptions);
});


function _showImages() {
	$("#dpi-thumbnails a[rel^='{/literal}{$config.detailed_product_images.dpi_images_viewer}{literal}']:first").click();
}
{/literal}

//-->
</script>