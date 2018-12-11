{include_once_src file="main/include_js.tpl" src="addons/magnifier/popup.js"}
{capture name=section}
<div class="box">

<div class="dialog_title">{$lng.txt_zoom_images_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="magnifier_upload_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="product_zoomer" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}
<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}<br/>
{/if}

<table class="header" width="100%">
<tr>
	{if $ge_id}<th width="15">&nbsp;</td>{/if}
	<th width="15">&nbsp;</td>
	<th width="65">{$lng.lbl_image}</td>
	<th width="5%">{$lng.lbl_pos}</td>
	<th width="25%">{$lng.lbl_availability}</td>
	<th width="50%">{$lng.lbl_image_size}</td> 
</tr>

{if $zoomer_images}
{foreach from=$zoomer_images item=image}
<tr {cycle values=", class='cycle'"}>
{if $ge_id}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td><input type="checkbox" value="Y" name="iids[{$image.image_id}]" /></td>
	<td align="center" >
<a href="javascript: void(0);" onclick="javascript: popup_magnifier('{$product_id}', '{#magnifier_x#}', '{#magnifier_y#}', '{$image.image_id}');"><img src="{$image.tmbn_url}" alt="" width="80" /></a>
	</td>
	<td ><input type="text" size="5" maxlength="5" name="zoomer_image[{$image.image_id}][orderby]" value="{$image.orderby}" /></td>
	<td align="center">
        {include file='main/select/availability.tpl' name="zoomer_image[`$image.image_id`][avail]" value=$image.avail}
	</td>
	<td align="center">{$image.image_x}x{$image.image_y}</td>
</tr>
{/foreach}
{else}
<tr>
	{if $ge_id}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="6" align="center">{$lng.txt_no_images}</td>
</tr>
{/if}
</table>



{if $zoomer_images}
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('magnifier_upload_form', 'zoomer_update_availability');" acl=$page_acl}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('magnifier_upload_form', 'product_zoomer_delete');" acl=$page_acl}
{/if}

{if $accl.$page_acl}
<div class="input_field_1">
    <label>
        {if $ge_id}<input type="checkbox" value="1" name="fields[new_z_image]" />{/if}
        {$lng.lbl_select_file}
    </label>
    {include file='main/images/edit.tpl' image='' button_name=$lng.lbl_browse in_type='magnifier_images' idtag='edit_magnifier_images'}
</div>
<div class="buttons">
{include file='buttons/button.tpl' button_title=$lng.lbl_upload href="javascript: cw_submit_form('magnifier_upload_form');"}
</div>
{/if}
</form>

</div>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_zoom_images}

