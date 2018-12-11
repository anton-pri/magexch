<script type="text/javascript">
//<!--

var windowWidth = "{$config.detailed_product_images.dpi_zoom_window_width|default:'500'}";
var windowHeight = "{$config.detailed_product_images.dpi_zoom_window_height|default:'300'}";

{literal}
/* available options
zoomWidth 	The width of the zoom window in pixels. If 'auto' is specified, the width will be the same as the small image. 	'auto'
zoomHeight 	The height of the zoom window in pixels. If 'auto' is specified, the height will be the same as the small image. 	'auto'
position 	Specifies the position of the zoom window relative to the small image. Allowable values are 'left', 'right', 'top', 'bottom', 'inside' or you can specifiy the id of an html element to place the zoom window in e.g. position: 'element1' 	'right'
adjustX 	Allows you to fine tune the x-position of the zoom window in pixels. 	0
adjustY 	Allows you to fine tune the y-position of the zoom window in pixels. 	0
tint 	Specifies a tint colour which will cover the small image. Colours should be specified in hex format, e.g. '#aa00aa'. Does not work with softFocus. 	false
tintOpacity 	Opacity of the tint, where 0 is fully transparent, and 1 is fully opaque. 	0.5
lensOpacity 	Opacity of the lens mouse pointer, where 0 is fully transparent, and 1 is fully opaque. In tint and soft-focus modes, it will always be transparent. 	0.5
softFocus 	Applies a subtle blur effect to the small image. Set to true or false. Does not work with tint. 	false
smoothMove 	Amount of smoothness/drift of the zoom image as it moves. The higher the number, the smoother/more drifty the movement will be. 1 = no smoothing. 	3
showTitle 	Shows the title tag of the image. True or false. 	true
titleOpacity 	Specifies the opacity of the title if displayed, where 0 is fully transparent, and 1 is fully opaque. 	0.5
*/

var zoomifierOptions = "adjustX: 10, zoomWidth: windowWidth, zoomHeight: windowHeight, showTitle: true, titleOpacity: 0.5";

$(document).ready(function(){

	if (productImgObj != undefined && productImageAnchorObj != undefined) {

		productImageAnchorObj.attr('href', productImgObj.attr('src'));
		productImageAnchorObj.attr('rel', zoomifierOptions);
		productImageAnchorObj.addClass('cloud-zoom');
		
		$('#dpi-thumbnails a').each(function() {
			imageIndex = $(this).find('img:first').attr('rel');
			if (imageIndex != undefined && imageIndex != null) {
				$(this).attr('rel', "useZoom: 'product-image-anchor', smallImage: '" + productImages[imageIndex] + "'");
				$(this).addClass('cloud-zoom-gallery');
			}
		});
		$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
        $('#dpi-thumbnails a:first').click();
	}

});


function _showImages() {
	$("#dpi-thumbnails a[rel^='{/literal}{$config.detailed_product_images.dpi_images_viewer}{literal}']:first").click();
}
{/literal}

//-->
</script>
