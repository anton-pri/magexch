{if $ajax_filter neq 1}
{load_defer file="jquery/jquery.min.js" type="js"}
{load_defer file="jquery/jquery-ui.min.js" type="js"}

{load_defer file="jquery/validation/jquery.validate.min.js" type="js"}
{load_defer file="jquery/validation/additional-methods.min.js" type="js"}
{load_defer file="jquery/jquery.blockUI.js" type="js"}
{load_defer file="jquery/jquery.blockUI.defaults.js" type="js"}
{load_defer file="jquery/jquery.jcarousel.js" type='js'}
{load_defer file="js/ajax.js" type="js"}
{load_defer file="jquery/form/jquery.form.js" type="js"}
{load_defer file="js/owl.carousel.js" type="js"}
{load_defer file="js/mousewheel.js" type="js"}
{load_defer file="js/easing.js" type="js"}
{load_defer file="js/html5.js" type="js"}
{load_defer file="js/select_all_checkboxes.js" type="js"}
{load_defer file="jquery/jquery.lingsTooltip.min.js" type="js"}
{load_defer file="js/render_tooltip.js" type="js"}
{load_defer file="jquery/jquery.tagcanvas.min.js" type="js"}
{load_defer file="jquery/file_upload/jquery.ui.widget.js" type="js"}
{load_defer file="jquery/file_upload/jquery.fileupload.js" type="js"}
{load_defer file="jquery/jquery.equalheights.js" type="js"}

{if $config.Appearance.infinite_scroll eq 'Y' && $infinite_scroll_manual_off ne 'Y'}
	{load_defer file="js/jquery-ias.min.js" type="js"}
{/if}
{/if}

{if $include.js}
{foreach from=$include.js item=file}
{load_defer file="`$file`" type="js"}
{/foreach}
{/if}

{load_defer file='customer/product-filter/product-filter.js' type='js'}

{load_defer_code type="js"}

{literal}
<script type="text/javascript">
function social_media_closePopup () {
    $('.ui-dialog-titlebar-close').click();
    sm('social_block', 250, 300, 1, 'Social Media Login');
}
$(document).ready(function() {
    $(".product_slide").owlCarousel({
        items : 5,
        navigationText: ["<i class='icon-chevron-left'></i>","<i class='icon-chevron-right'></i>"],
        navigation: true,
        pagination: false
    });
});

$("#social_link").click(function(){
    $('#social_link').live('click',sm('social_block', 250, 300, 1, 'Social Media Login'));
});
</script>
{/literal}

{if $config.Appearance.infinite_scroll eq 'Y' && $infinite_scroll_manual_off ne 'Y' && $products}
{literal}
<script type="text/javascript">
$(document).ready(function() {
	jQuery.ias({
	    container : '#product_list',
	    item: '.item',
	    pagination: '.navigation_pages',
	    next: '.nav_bottom .next',{/literal}
	    loader: '<img src="{$ImagesDir}/loader.gif"/>',{literal}
	    triggerPageThreshold: 20,
	});
});
</script>
{/literal}
{/if}

{literal}
<script type="text/javascript">
	$(document).ready(function() {
		jQuery.extend(jQuery.validator.messages, {
			required: lbl_field_is_required
		});
 
        if (customer_id == 0) {
            $('.need_login').live('click',cw_login_dialog);
        }


	});
</script>
{/literal}

{if $app_config_file.interface.uniform}
    {* set "uniform" param under [interface] chapter to false in config.local.ini to avoid styled selectboxes *}
    {load_defer file="jquery/15-jquery.uniform-modified.js" type="js"}
{/if}

{load_defer_code type="js"}
