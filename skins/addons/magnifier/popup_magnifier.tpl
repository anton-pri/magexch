{if $zoomer_images_count}
{include_once_src file='main/include_js.tpl' src='addons/magnifier/popup.js'}
<a href="javascript: void(0);" onclick="popup_magnifier('{$product.product_id}');"  class="magnify_link">{include file='common/thumbnail.tpl' image=$product.image_det id='product_thumbnail'}</a>
<a href="javascript: void(0);" onclick="popup_magnifier('{$product.product_id}');" class="magnify_link">{$lng.lbl_click_to_zoom}</a>
{else}
{include file='common/thumbnail.tpl' image=$product.image_det id='product_thumbnail'}
{/if}