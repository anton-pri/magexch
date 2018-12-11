<script type="text/javascript">
//<!--

{literal}
var imageGalleryOptions = {
	type: 'image',				/* Forces content type. Can be set to 'image', 'ajax', 'iframe', 'swf' or 'inline' */
	padding: 10,				/* Space between FancyBox wrapper and content */
	margin: 20, 				/* Space between viewport and FancyBox wrapper */
	opacity: false,				/* When true, transparency of content is changed for elastic transitions */
	modal: false, 				/* When true, 'overlayShow' is set to 'true' and 'hideOnOverlayClick', 'hideOnContentClick', 'enableEscapeButton', 'showCloseButton' are set to 'false' */
	cyclic: false, 				/* When true, galleries will be cyclic, allowing you to keep pressing next/back. */
	scrolling: 'auto', 			/* Set the overflow CSS property to create or hide scrollbars. Can be set to 'auto', 'yes', or 'no' */
	autoScale: true, 			/* If true, FancyBox is scaled to fit in viewport */
	autoDimensions: true,		/* For inline and ajax views, resizes the view to the element recieves. Make sure it has dimensions otherwise this will give unexpected results */
	centerOnScroll: false,		/* When true, FancyBox is centered while scrolling page */
	swf: null,		 			/* {wmode: 'transparent'} 	Params to put on the swf object */
	hideOnOverlayClick: true,	/* Toggle if clicking the overlay should close FancyBox */
	hideOnContentClick: false,	/* Toggle if clicking the content should close FancyBox */
	overlayShow: true,			/* Toggle overlay */
	overlayOpacity: 0.3,		/* Opacity of the overlay (from 0 to 1; default - 0.3) */
	overlayColor: '#666',		/* Color of the overlay */
	titleShow: true,			/* Toggle title */
	titlePosition: 'over',		/* The position of title. Can be set to 'outside', 'inside' or 'over' */
	titleFormat: null,			/* Callback to customize title area. You can set any html - custom image counter or even custom navigation */
	transitionIn: 'fade',		/* The transition type. Can be set to 'elastic', 'fade' or 'none' */
	transitionOut: 'fade',		/* The transition type. Can be set to 'elastic', 'fade' or 'none' */
	speedIn: 300,				/* Speed of the fade and elastic transitions, in milliseconds */
	speedOut: 300,				/* Speed of the fade and elastic transitions, in milliseconds */
	changeSpeed: 300,			/* Speed of resizing when changing gallery items, in milliseconds */
	changeFade: 'fast',			/* Speed of the content fading while changing gallery items */
	easingIn: 'swing',			/* Easing used for elastic animations */
	easingOut: 'swing',			/* Easing used for elastic animations */
	showCloseButton: true,		/* Toggle close button */
	showNavArrows: true, 		/* Toggle navigation arrows */
	enableEscapeButton: true,	/* Toggle if pressing Esc button closes FancyBox */
	onStart: function() { $("#dpi-thumbnails").trigger("pause"); }, /* Will be called right before attempting to load the content */
	onClosed: function() { $("#dpi-thumbnails").trigger("play"); }, /* Will be called once FancyBox is closed */
};

$(document).ready(function(){
	$('#dpi-thumbnails a').fancybox(imageGalleryOptions);
});


function _showImages() {
	$("#dpi-thumbnails a[rel^='{/literal}{$config.detailed_product_images.dpi_images_viewer}{literal}']:first").click();
}
{/literal}

//-->
</script>
