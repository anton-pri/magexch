<script type="text/javascript">
//<!--
var theme = "facebook";
{if $config.detailed_product_images.dpi_theme ne ''}
	theme = "{$config.detailed_product_images.dpi_theme|escape:'javascript'}";
{/if}
var dwidth = "{$dimage_width}";
var dheight = "{$dimage_height}";

{literal}
var imageGalleryOptions = {
	animation_speed: 'fast', 		/* fast/slow/normal */
	slideshow: 5000, 				/* false OR interval time in ms */
	autoplay_slideshow: false, 		/* true/false */
	opacity: 0.80, 					/* Value between 0 and 1 */
	show_title: true, 				/* true/false */
	allow_resize: false, 			/* Resize the photos bigger than viewport. true/false */
	default_width: dwidth,
	default_height: dheight,
	counter_separator_label: '/', 	/* The separator for the gallery counter 1 "of" 2 */
	theme: theme, 					/* light_rounded / dark_rounded / light_square / dark_square / facebook */
	hideflash: false, 				/* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
	modal: false, 					/* If set to true, only the close button will close the window */
	overlay_gallery: true, 			/* If set to true, a gallery will overlay the fullscreen image on mouse over */
	keyboard_shortcuts: false, 		/* Set to false if you open forms inside prettyPhoto */
	changepicturecallback: function(){{/literal}{if $config.detailed_product_images.dpi_viewer_thumbnails_position eq 'top'}{literal}galleryHeight = $pp_pic_holder.find('.pp_gallery').height(); galleryMarginTop = $pp_pic_holder.find('.pp_content').height()-52; $pp_pic_holder.find('.pp_gallery').css('margin-top',-galleryMarginTop);{/literal}{/if}{literal} $("#dpi-thumbnails").trigger("pause"); },
	callback: function() { $("#dpi-thumbnails").trigger("play"); }
};

$(document).ready(function(){
	$("a[rel^='prettyPhoto']").prettyPhoto(imageGalleryOptions);
});



function _showImages() {
	$("#dpi-thumbnails a[rel^='{/literal}{$config.detailed_product_images.dpi_images_viewer}{literal}']:first").click();
}
{/literal}
//-->
</script>