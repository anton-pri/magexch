{capture name=section}


<form action="index.php?target={$current_target}" method="post" name="update_product_video_form">
  <input type="hidden" name="mode" value="details" />
  <input type="hidden" name="js_tab" value="product_video" />
  <input type="hidden" name="action" value="update_video" />
  <input type="hidden" name="product_id" value="{$product.product_id}" />

  <table class="table table-striped dataTable vertical-center" width="100%">
  <thead>
    <tr>
      <th>{if $product_video}<input type='checkbox' class='select_all' class_to_select='product_video_item' />{else}&nbsp;{/if}</th>
      <th>{$lng.lbl_order}</th>
      <th>{$lng.lbl_title}</th>
      <th>{$lng.lbl_descr}</th>
      <th>{$lng.lbl_code}</th>
    </tr>
  </thead>
    {if $product_video}

    {foreach from=$product_video item=video}
    <tr{cycle values=', class="cycle"'}>
      <td align="center"><input type="checkbox" value="Y" name="video[{$video.video_id}][delete]" class="product_video_item" /></td>
      <td align="center"><input type="text" size="6" maxlength="11" name="video[{$video.video_id}][pos]" value="{$video.pos|default:0}" /></td>
      <td><input type="text" name="video[{$video.video_id}][title]" value='{$video.title|escape}' /></td>
      <td align="center"><textarea name="video[{$video.video_id}][descr]" cols='40'>{$video.descr}</textarea></td>
      <td align="center"><textarea name="video[{$video.video_id}][code]" cols='40'>{$video.code}</textarea></td>
    </tr>
    {/foreach}
    <tr>
      <td colspan="5"><img src="{$ImagesDir}/spacer.gif" height="10px" class="Spc" alt="" /></td>
    </tr>
    <tr>
      <td colspan="5">
        {include file='buttons/button.tpl' href="javascript:cw_submit_form('update_product_video_form');" button_title=$lng.lbl_update}
        {include file='buttons/button.tpl' href="javascript:cw_submit_form('update_product_video_form', 'delete_video');" button_title=$lng.lbl_delete_selected}
      </td>
    </tr>

    {else}
    <tr>
      <td colspan="5" align="center">{$lng.txt_pt_no_elements}</td>
    </tr>
    {/if}
    </table>

{include file='common/subheader.tpl' title=$lng.lbl_add_new}


  <table class="table table-striped dataTable vertical-center" width="100%">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th>{$lng.lbl_order}</th>
      <th>{$lng.lbl_title}</th>
      <th>{$lng.lbl_descr}</th>
      <th>{$lng.lbl_code}</th>
    </tr>
  </thead>
    <tr{cycle values=', class="cycle"'} valign='top'>
      <td align="center">&nbsp;</td>
      <td align="center"><input class="form-control" type="text" size="6" maxlength="11" name="new_video[pos]" value="{$video.pos|default:0}" /></td>
      <td><input type="text" class="form-control" name="new_video[title]" value='' /></td>
      <td align="center"><textarea name="new_video[descr]" class="form-control" cols='40'></textarea></td>
      <td align="center"><textarea name="new_video[code]" class="form-control" cols='40'></textarea></td>
    </tr>

    </table>
    {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_product_video_form', 'add_video');" button_title=$lng.lbl_add style="btn-green push-20"}

</form>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_product_video content=$smarty.capture.section}
