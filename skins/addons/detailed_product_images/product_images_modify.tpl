{if $addons.detailed_product_images}
{capture name=section}
<div class="dialog_title">{$lng.txt_det_images_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="upload_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="product_images" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}<br/>{/if}

<table  class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    <th width="15">&nbsp;</th>
    <th width="115">{$lng.lbl_image}</th>
    <th width="5%">{$lng.lbl_pos}</th>
    <th width="15%" class="text-center">{$lng.lbl_availability}</th>
    <th width="40%">{$lng.lbl_alternative_text}</th>
    <th width="20%">{$lng.lbl_image_properties}</th>
</tr>
</thead>
{if $images}
{foreach from=$images item=image}
<tr{cycle values=", class='cycle'"}>
{if $ge_id}<td><input type="checkbox" value="Y" name="fields[d_image][{$image.image_id}]" /></td>{/if}
	<td><input type="checkbox" value="Y" name="iids[{$image.image_id}]" /></td>
	<td align="center"><a href="{$image.tmbn_url}" target="_blank"><img src="{$image.tmbn_url}" width="100" alt="" /></a></td>
	<td>
        <input class="form-control" type="text" size="5" maxlength="5" name="image[{$image.image_id}][orderby]" value="{$image.orderby}" />
	</td>
	<td align="center">
        {include file='admin/select/availability.tpl' name="image[`$image.image_id`][avail]" value=$image.avail}
	</td>
	<td>    
        <input type="text" class="form-control" name="image[{$image.image_id}][alt]" value="{$image.alt}" size="30" />
    </td>
    <td>
{$image.type},
{$image.image_x}x{$image.image_y},
{$image.image_size}b
    </td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="7" align="center">{$lng.txt_no_images}</td>
</tr>
{/if}
</table>
{if $images}
<div class="buttons bottom">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('upload_form', 'update_availability');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('upload_form', 'product_images_delete');" style="btn-danger push-20 push-5-r"}
</div>
{/if}
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_detailed_images content=$smarty.capture.section}
{capture name=section2}
{if $accl.$page_acl}
<div class="form-horizontal">
{include file='common/subheader.tpl' title=$lng.txt_add_new_detail_image}
<div class="form-group">
{if $ge_id}<input type="checkbox" value="1" name="fields[new_d_image]" />{/if}
    <label class="col-xs-12">{$lng.lbl_select_file}:</label>
    <div class="col-xs-12">{include file='admin/images/edit.tpl' button_name=$lng.lbl_browse in_type='products_detailed_images' idtag='edit_products_detailed_images'}</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_alternative_text}</label>
    <div class="col-xs-12"><input type="text" size="45" name="alt" value="" class="form-control" /></div>
</div>
<div class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_upload href="javascript: cw_submit_form('upload_form');" style="btn-green push-20"}
</div>
</div>
{/if}
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.txt_add_new_detail_image content=$smarty.capture.section2}
{/if}
