{capture name=section}
{capture name=block}

<form action='' method='POST' name='maintenance_form' class="form-horizontal">

  <div class="form-group" >
    <label class="col-xs-12">Any label:</label>
    <div class="col-xs-12">
    	<textarea  class="form-control" ></textarea>
    </div>
  </div>
  <div class="form-group" >
    <label class="col-xs-12">Any other label:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" value="" maxlength="64" name="" >
    </div>
  </div>
  <div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_submit href="javascript: cw_submit_form(''maintenance_form');" style="btn-green push-20"}</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_maintenance}

{capture name=section}
<form action='index.php' method='POST' name='images_convert_form'>
  <input type="hidden" name="target" value="maintenance_images_convert">
  <div class="input_field_1" >
    <label class='required'>{$lng.lbl_convert_images}:</label>
    <select name="image_convert_data[image_types][]" multiple>
    {foreach from=$available_images item=ai key=ai_name}
    <option value="{$ai_name}">{$ai_name} ({$ai.http_files_count} remote images)</option>
    {/foreach}
    </select>
  </div>
  <div class="input_field_1" >
    <label class='required'>{$lng.lbl_replace_deleted_images_with_default|default:'Replace deleted images with default image'}:</label>
    <input type="checkbox" name="image_convert_data[replace_deleted]" value="1" />
  </div>
  <div class="buttons">{include file='buttons/button.tpl' button_title=$lng.lbl_submit href="javascript: cw_submit_form('images_convert_form');"}</div>

</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_convert_remote_images}
