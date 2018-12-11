{include_once_src file='main/include_js.tpl' src='main/popup_image_selection.js'}
{if $mode eq 'add'}
{include file='main/images/edit.tpl' image='' button_name=$lng.lbl_select in_type='customers_images'}
{include file='buttons/button.tpl' button_title=$lng.lbl_upload href="javascript:cw_submit_form('upload_form');"}
{else}
<table class="header" width="50%">

<tr>
    <th>&nbsp;</th>
    <th>{$lng.lbl_image}</th>
</tr>
{foreach from=$photos item=photo}
{xcm_thumb
src_url=$photo.tmbn_url
width=300
height=300
assign_url="photo_img_url"
assign_x="photo_img_x"
assign_y="photo_img_y"
no_zoom="Y"}
<tr> 
    <td valign="top" align="center"><input type="checkbox" name="del[{$photo.image_id}]" value="1"></td>
    <td style="padding: 0; text-align: center;">{*include file="common/thumbnail.tpl" image=$photo *}<img src="{$photo_img_url}" width="{$photo_img_x}" height="{$photo_img_y}" /><br />{$lng.lbl_dimentions}: {$photo.image_x}x{$photo.image_y}<br />{$lng.lbl_file}: {$photo.filename}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="2 align="center">{$lng.lbl_not_found}</td>
{/foreach}
</table>
{if $photos}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('photos_form');" style="button"}
{/if}
{/if}
