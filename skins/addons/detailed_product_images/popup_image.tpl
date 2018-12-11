{if $images}
	{assign var='number_images' value=$images|@count}
	{assign var='thumb_displ_type' value=$config.detailed_product_images.dpi_thumbnails_panel_type|default:'vpanel'}

	{if $config.detailed_product_images.dpi_display_thumbs eq 'Y'}
		{if $thumb_displ_type == 'table'}
			{include_once_src file='main/include_js.tpl' src='addons/detailed_product_images/js/thumbnails.js'}
		{/if}
		{if $thumb_displ_type == 'vpanel' || $thumb_displ_type == 'hpanel'}
			{include_once_src file='main/include_js.tpl' src='addons/detailed_product_images/js/jquery.carouFredSel.js'}
		{/if}
	{/if}
       <!-- cw@image_link [ -->

	<a href="#" id="product-image-anchor" onclick="showImages(); return false;" style="display: block; min-height: {$config.Appearance.products_images_det_width+10}px; min-width: {$config.Appearance.products_images_det_width}px;">
		{include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_det id='product_thumbnail'}
	</a>
       <!-- cw@image_link ] -->

       <!-- cw@product_thumbs [ -->

	{if $config.detailed_product_images.dpi_display_thumbs ne 'Y'}
		{if $viewers_exist}
			{assign var='viewer_type' value=$config.detailed_product_images.dpi_images_viewer|regex_replace:"/(.*)(zoom)(.*)/i":"zoomifier"}
			{if $viewer_type ne "zoomifier"}
				<br /><a href="#" onclick="showImages(); return false;">{$lng.lbl_click_to_enlarge}</a>
			{/if}
		{/if}
	{/if}
       <!-- cw@product_thumbs ] -->

	{assign var='thumb_width' value=$config.detailed_product_images.dpi_thumbnail_width|default:"60"}
	{assign var='thumb_height' value=$config.detailed_product_images.dpi_thumbnail_height|default:"60"}
	{assign var='dimage_width' value=$config.detailed_product_images.dpi_image_width|default:"500"}
	{assign var='dimage_height' value=$config.detailed_product_images.dpi_image_height|default:"344"}
	{assign var='primage_width' value=$available_images.products_images_det.max_width|default:"150"}

	{if $config.detailed_product_images.dpi_images_viewer eq 'prettyPhoto'}
		{assign var='rel_value' value="`$config.detailed_product_images.dpi_images_viewer`[]"}
	{else}
		{assign var='rel_value' value=$config.detailed_product_images.dpi_images_viewer}
	{/if}

	{strip}
       <div class="clear"></div>
	<div id="dpi-thumbnails-outer">
		<ul id="dpi-thumbnails">
	{foreach from=$images key=k item=dpi_image name=dimage}
		{xcm_thumb src_url=$dpi_image.tmbn_url assign_url="images.`$smarty.foreach.dimage.index`.tmbn_url" assign_x="images.`$smarty.foreach.dimage.index`.image_x" assign_y="images.`$smarty.foreach.dimage.index`.image_y" width=$dimage_width height=$dimage_height keep_file_h2w="Y"}
		{xcm_thumb src_url=$dpi_image.tmbn_url assign_url="images.`$smarty.foreach.dimage.index`.dpi_primage_url" width=$primage_width assign_x="images.`$smarty.foreach.dimage.index`.dpi_primage_x" assign_y="images.`$smarty.foreach.dimage.index`.dpi_primage_y" keep_file_h2w="Y"}
		{xcm_thumb src_url=$dpi_image.tmbn_url assign_url="images.`$smarty.foreach.dimage.index`.dpi_thumbnail_url" width=$thumb_width height=$thumb_height assign_x="images.`$smarty.foreach.dimage.index`.dpi_thumb_x" assign_y="images.`$smarty.foreach.dimage.index`.dpi_thumb_y" keep_file_h2w="N"}
		<a class="dpi-thumb" {if $dpi_image.variant_id}variant_id='{$dpi_image.variant_id}'{/if} rel="{$rel_value}" href="{$images[$k].tmbn_url}" title="{$dpi_image.alt|escape:'html'}"><img rel="{$dpi_image.in_type}_{$dpi_image.image_id}" alt="{$dpi_image.alt|escape:'html'}" src="{$images[$k].dpi_thumbnail_url}" height="{$images[$k].dpi_thumb_y}" width="{$images[$k].dpi_thumb_x}" title="{$dpi_image.alt|escape:'html'}" /></a>
	{/foreach}
		</ul>
		<div class="dpi-clearfix"></div>
		<a rel="history" class="dpi-prev" id="dpi-thumbnails-prev" href="#" onclick="return false;" title="Previous thumbnail"><span>prev</span></a>
		<a rel="history" class="dpi-next" id="dpi-thumbnails-next" href="#" onclick="return false;" title="Next thumbnail"><span>next</span></a>
	</div>
       {*<div class="dpi_note">{$lng.lbl_dpi_note}</div>*}
       <div class="clear"></div>
	{/strip}

	<script type="text/javascript">
	//<!--

	var current_viewer = "{$config.detailed_product_images.dpi_images_viewer|default:'null'}";
	var defaultProductImgId = 'product_thumbnail';
//    var productImageSrc = '{$product.image_det.tmbn_url}';
	var productImgObj = new Object();
	var productImageAnchorObj = new Object();
	var is_zoomifier = false;
	var number_visible_thumbs = {$config.detailed_product_images.dpi_number_visible_thumbs|default:1};
	var numberThumbs = {$number_images};
	var number_thumbs_scroll = {$config.detailed_product_images.dpi_number_thumbs_scroll|default:1};

	var productImages = new Array();
	{foreach from=$images key=k item=dpi_image name=dimage}
		productImages['{$dpi_image.in_type}_{$dpi_image.image_id}'] = "{$dpi_image.dpi_primage_url}";
	{/foreach}
    var all_images_count = '{$images|@count}';
	{literal}
	$(document).ready(function() {

		if (current_viewer != null) {
			var _current_viewer = current_viewer.toLowerCase();
			var zoomr_pattern = new RegExp("zoom");
			is_zoomifier = zoomr_pattern.test(_current_viewer);
		}

		if (is_zoomifier != false) {
			numberThumbs += 1;
		}

		if (number_visible_thumbs > numberThumbs) {
			number_visible_thumbs = numberThumbs;
		}
		if (number_thumbs_scroll > numberThumbs) {
			number_thumbs_scroll = 1;
		}


		productImgObj = $("#" + defaultProductImgId);
		if (productImgObj == undefined) {
			productImgObj = $("div.image a img:first");
			if (productImgObj != undefined) {
				if (productImgObj.attr('id') == undefined || productImgObj.attr('id') == null) {
					productImgObj.attr('id', defaultProductImgId);
				}
				defaultProductImgId = productImgObj.attr('id');
			}
		}
		var productImgSrc = null;

		if (productImgObj != undefined) {
			if (productImgObj.attr('src') == undefined) {
				productImgObj = undefined;
			} else {
				productImgSrc = productImgObj.attr('src');
			}
		}

		productImageAnchorObj = $('#product-image-anchor');

		var config = {
			mouseOutOpacity:   0.67,
			mouseOverOpacity:  1.0,
			fadeSpeed:         'fast',
			exemptionSelector: '.selected'
	    };

		function fadeTo(element, opacity) {
	        var $target = $(element);

	        if (config.exemptionSelector)
	                $target = $target.not(config.exemptionSelector);

	        $target.fadeTo(config.fadeSpeed, opacity);
		}
	{/literal}
	{if $config.detailed_product_images.dpi_display_thumbs eq 'Y'}
	{literal}

		if (is_zoomifier != false) {
			$('#dpi-thumbnails a').bind('markthumb',
				function(e, obj) {
					$('#dpi-thumbnails a').filter('.dpi-selected').removeClass('dpi-selected');
					obj.addClass('dpi-selected');
					e.preventDefault();
				}
			);
/*
			_first_thumb = $('#dpi-thumbnails a:first').clone();
			_first_thumb.attr('href', productImageSrc);
			_new_index = productImages.length;
			productImages[_new_index] = productImageSrc;//productImgObj.attr('src');
			_first_thumb.find('img:first').attr('src', productImageSrc).attr('rel', _new_index);
			_first_thumb.prependTo($('#dpi-thumbnails'));
*/
        }

		var onMouseOutOpacity = 0.67;
		$('#dpi-thumbnails a').css('opacity', onMouseOutOpacity).hover(
			function() {
				fadeTo(this, config.mouseOverOpacity);
				if (is_zoomifier == false) {
					var currentImageObj = $(this).find('img:first');
					if (currentImageObj != undefined) {
						imgeSrc = currentImageObj.attr('rel');
						if (imgeSrc != null) {
							imgeSrc = productImages[imgeSrc];
							if (imgeSrc != null) {
								if (productImgObj != undefined) {
									productImgObj.attr('src', imgeSrc);
								}
							}
						}
					}
				}
			},
			function() {
				fadeTo(this, config.mouseOutOpacity);
				if (is_zoomifier == false) {
					if (productImgObj != undefined && productImgSrc != null) {
						productImgObj.attr('src', productImgSrc);
					}
				}
			}
		);
	{/literal}

		{if $thumb_displ_type == 'vpanel' || $thumb_displ_type == 'hpanel'}
			var thumbnails_position = "up";

			{if $thumb_displ_type == 'hpanel'}
				thumbnails_position = "left";
			{/if}
	{literal}
			if (all_images_count > 1) $("#dpi-thumbnails-outer").show();
			$("#dpi-thumbnails").carouFredSel({
				circular	: false,					/* Determines whether the carousel should be circular */
				direction	: thumbnails_position,		/* "right", "left", "up" or "down", The direction to scroll the carousel, determines whether the carousel scrolls horizontal or vertical and -when the carousel scrolls automatically- in what direction */
				width		: null,						/* The width of the carousel, if null, the width is calculated automatically */
				height		: null,						/* The height of the carousel, if null, the height is calculated automatically */
				items		: number_visible_thumbs,	/* A number for items.visible */
				scroll		: number_thumbs_scroll,		/* The number of items to scroll */
				auto		: false,					/* Determines whether the carousel should scroll automatically or not */
				prev		: "#dpi-thumbnails-prev",	/* A jQuery-selector for the HTML element that should scroll the carousel backward */
				next		: "#dpi-thumbnails-next",	/* A jQuery-selector for the HTML element that should scroll the carousel forward */
				pagination 	: undefined					/* A jQuery-selector for the HTML element that should contain the pagination-links */
			});

//			var all_images_count = $('#dpi-thumbnails').find('a').length;

			if (thumbnails_position == 'up') {

				if (number_visible_thumbs < all_images_count) {
					$('#dpi-thumbnails-outer').addClass('vert');
				}
				$('#dpi-thumbnails-prev').addClass('vert');
				$('#dpi-thumbnails-next').addClass('vert');
				var _buttonLeft = $('#dpi-thumbnails-outer').width()/2-$('#dpi-thumbnails-next').width()/2;
				$('#dpi-thumbnails-next').css('left', _buttonLeft);
				$('#dpi-thumbnails-prev').css('left', _buttonLeft);
			}
			else {
				var _buttonTop = $('#dpi-thumbnails-outer').outerHeight()/2-$('#dpi-thumbnails-next').height()/2;
				$('#dpi-thumbnails-next').css('top', _buttonTop);
				$('#dpi-thumbnails-prev').css('top', _buttonTop);
			}

			if (number_visible_thumbs >= all_images_count) {
				$('#dpi-thumbnails-next, #dpi-thumbnails-prev').hide();
			}
	{/literal}
		{else}
			$("#dpi-thumbnails-outer").show();
			$('#dpi-thumbnails-outer').addClass('table');
			$('#dpi-thumbnails-prev').addClass('table');
			$('#dpi-thumbnails-next').addClass('table');
			{literal}

			var _wrapperWidth = $('div.image').width();
			var _thumbnailsWidth = 0;
			var _tmpWidth = $('#dpi-thumbnails').find('a:first').outerWidth();
			var _c=1;
//			var all_images_count = $('#dpi-thumbnails').find('a').length;

			$('#dpi-thumbnails').find('a').each(function() {
				_currentElmWidth = 0;
				_currentElmWidth += parseInt($(this).css('margin-left'));
				_currentElmWidth += parseInt($(this).css('margin-right'));
				_currentElmWidth += $(this).outerWidth();
				_thumbnailsWidth += _currentElmWidth;

				if (_thumbnailsWidth > _wrapperWidth) {
					_thumbnailsWidth = _tmpWidth;
					return false;
				}
				_tmpWidth = _thumbnailsWidth;

				if (number_visible_thumbs == _c) {

					if (number_visible_thumbs >= all_images_count) {
						$('#dpi-thumbnails-next, #dpi-thumbnails-prev').hide();
					}
					return false;
				}
				_c += 1;
			});
			$('#dpi-thumbnails').width(_thumbnailsWidth);

			var gallery = $('#dpi-thumbnails-outer').thumbnails({numThumbs: number_visible_thumbs});

			if (number_visible_thumbs >= all_images_count) {
				$('#dpi-thumbnails-next, #dpi-thumbnails-prev').hide();
			}
	{/literal}
		{/if}
	{else}
	{literal}
		$("#dpi-thumbnails-outer").css('position', 'absolute').css('top', '-3000px').css('left', '-5000px');
		$("#dpi-thumbnails-outer").show();
	{/literal}
	{/if}
	{literal}
        if (all_images_count == 1) $('#dpi-thumbnails').hide();
	});

	function showImages() {
		{/literal}
		{if $viewers_exist == false}
			popup_image('products_detailed_images', '{$product.product_id}', '{$max_x}', '{$max_y}', '{$product.product|escape:"url"}');
		{else}
			_showImages();
		{/if}
		{literal}
	}

	{/literal}
	//-->
	</script>

	{if $viewers_exist == true}
		<link rel="stylesheet" href="{$SkinDir}/addons/detailed_product_images/viewers/{$config.detailed_product_images.dpi_images_viewer}/css/{$config.detailed_product_images.dpi_images_viewer}.css" media="screen" charset="utf-8" />
		{if $config.detailed_product_images.dpi_theme}
			<link rel="stylesheet" href="{$SkinDir}/addons/detailed_product_images/viewers/{$config.detailed_product_images.dpi_images_viewer}/css/{$config.detailed_product_images.dpi_theme}.css" media="screen" charset="utf-8" />
		{/if}

		{include_once_src file='main/include_js.tpl' src="addons/detailed_product_images/viewers/`$config.detailed_product_images.dpi_images_viewer`/js/jquery.`$config.detailed_product_images.dpi_images_viewer`.js"}
		{include file="addons/detailed_product_images/viewers/`$config.detailed_product_images.dpi_images_viewer`/tpl/`$config.detailed_product_images.dpi_images_viewer`.tpl"}
	{/if}
{else}
	{include file='common/thumbnail.tpl' image=$product.image_det id='product_thumbnail' }
{/if}
