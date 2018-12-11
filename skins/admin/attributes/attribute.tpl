{*include file='common/page_title.tpl' title=$lng.lbl_features_modify*}
{capture name=section}

{capture name=block}

{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`&mode=att&attribute_id=`$attribute.attribute_id`"}
<script type="text/javascript">
	var attribute_description = '{$lng.lbl_description}';
{literal}
function cw_change_attribute_type(value) {

    var value_select = 'none';
    var value_multiselect = 'none';
    var value_text = 'none';
    var value_yes_no = 'none';
    var value_date = 'none';

    if (value == 'selectbox') value_select = '';
    else if (value == 'multiple_selectbox') value_multiselect = '';
    else if(value == 'date') value_date = '';
    else if(value == 'yes_no') value_yes_no = '';
    else value_text = '';

    $('#default_value_select').css('display', value_select);
    $('#default_value_multiselect').css('display', value_multiselect);
    $('#default_value_text').css('display', value_text);
    $('#default_value_yes_no').css('display', value_yes_no);
    $('#default_value_date').css('display', value_date);
}
$(document).ready(function() {
{/literal}
    cw_change_attribute_type('{$attribute.type|default:'text'}');
{literal}
    $('.protected').block({ message: null,theme:     true });
});
{/literal}
</script>
<form action="index.php?target={$current_target}&mode=att" method="post" name="attribute_modify_form" class="form-horizontal">
<input type="hidden" name="action" value="modify_att">
<input type="hidden" name="posted_data[attribute_id]" value="{$attribute.attribute_id}">

<div class="box">

{if $attribute.addon}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_addon}</label>
    <div class="col-xs-12">
    {assign var='langname' value="addon_name_`$attribute.addon`"}
    {tunnel func='cw_get_langvar_by_name' via='cw_call' param1=$langname param2='' param3=false param4=true}
    </div>
</div>
{/if}
<div class="form-group">
    <label class="col-xs-12">Type</label>
    <div class="col-xs-12">
    {assign var='langname' value="attribute_name_`$attribute.item_type`"}
    {tunnel func='cw_get_langvar_by_name' via='cw_call' param1=$langname param2='' param3=false param4=true}
    </div>
</div>
{if $attribute.protection}
<div class="form-group">
    <label class="col-xs-12">Protection</label>
    <div class='top_error col-xs-12'>{$lng.err_protected_attribute}</div>
</div>

{/if}


<div class="form-group {if $attribute.protection & $smarty.const.ATTR_PROTECTION_FIELD}protected{/if}">
    <label class="col-xs-12">{$lng.lbl_att_code}</label>
    <div class="col-xs-12">
    <input class="form-control" type="text" name="posted_data[field]" value="{$attribute.field|escape}" {if $attribute.protection & $smarty.const.ATTR_PROTECTION_FIELD} readonly {/if} />
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_position}</label>
    <div class="col-xs-12">
    <input class="form-control" type="text" name="posted_data[orderby]" value="{$attribute.orderby}" />
    </div>
</div>
<div class="form-group">
    <label class='multilan col-xs-12'>
        {$lng.lbl_att_name}
        
    </label>
    <div class="col-xs-12">
    <input class="form-control" type="text" name="posted_data[name]" value="{$attribute.name|escape}" />
    </div>
</div>
{include file='admin/attributes/attribute_field.tpl'}

<div class="form-group {if $attribute.protection & $smarty.const.ATTR_PROTECTION_TYPE}protected{/if}">
    <label class="col-xs-12">{$lng.lbl_att_type}</label>
    <div class="col-xs-12">
    {include file='admin/select/attribute_type.tpl' name='posted_data[type]' value=$attribute.type onchange="cw_change_attribute_type(this.value);"}
	</div>
</div>
<div {if $attribute.protection & $smarty.const.ATTR_PROTECTION_VALUES}class='protected'{/if}>
<!-- Values -->
<div class="form-group" id="default_value_text">
    <label class='multilan col-xs-12'> 
        {$lng.lbl_att_default_value}
    </label>
    <div class="col-xs-6">
		<input class="form-control" type="text" name="posted_data[default_value_text]" value="{$attribute.default_value|strval|escape}" />
    </div>
    <div class="col-xs-6">
    	{include file='admin/attributes/attribute_value_field.tpl'}
    </div>
    
	<label class="attr_value_label col-xs-12 push-10-t">
		{$lng.lbl_facet} 	
		<input type="checkbox" name="posted_data[default_value_text_facet]" value="1" {if $attribute.default_values.facet}checked="true"{/if}/>
	</label>
	<label class="attr_value_label col-xs-12">{$lng.lbl_description}</label>
	<div class="col-xs-12">
		<textarea class="form-control" name="posted_data[default_value_text_description]">{$attribute.default_values.description|escape}</textarea>
	</div>
	<div class="col-xs-12 push-10-t">
    	<a class="btn btn-default" href="index.php?target=attribute_options&attribute_id={$attribute.attribute_id}" target="_blank" title="{$lng.lbl_edit_options}"/>Edit values</a>
	</div>
</div>
<div class="form-group" id="default_value_yes_no">
    <label class="col-xs-12">{$lng.lbl_att_default_value}</label>
    <div class="col-xs-12">
    {include file='main/select/yes_no.tpl' name="posted_data[default_value_yes_no]" value=$attribute.default_value }
	<br><span class="attr_value_label">{$lng.lbl_facet}</span>
	<input type="checkbox" name="posted_data[default_value_yes_no_facet]" value="1" {if $attribute.default_values.facet}checked="true"{/if}/>
	<br><span class="attr_value_label">{$lng.lbl_description}</span>
	<textarea class="form-control" name="posted_data[default_value_yes_no_description]" style="width: 500px">{$attribute.default_values.description|escape}</textarea>
	</div>
</div>
<div class="form-group" id="default_value_select">
	<div class="col-xs-12">
    	<a href="index.php?target=attribute_options&attribute_id={$attribute.attribute_id}" target="_blank" title="{$lng.lbl_edit_options}"/>Edit values</a>
	</div>
    {*include file='admin/attributes/attribute_options_select.tpl' attribute=$attribute*}
</div>
<div class="form-group" id="default_value_multiselect">
	<div class="col-xs-12">
  	  <a href="index.php?target=attribute_options&attribute_id={$attribute.attribute_id}" target="_blank" title="{$lng.lbl_edit_options}"/>Edit values</a>
   	 {*include file='admin/attributes/attribute_options_multiselect.tpl' attribute=$attribute*}
	</div>
</div>
<div class="form-group" id="default_value_date">
    <label class="col-xs-12">{$lng.lbl_default_value}</label>
    <div class="col-xs-12">
    {html_select_date prefix="default_value_date" time=$attribute.default_value start_year=-10 end_year=+10}
	<br><span class="attr_value_label">{$lng.lbl_facet}</span>
	<input type="checkbox" name="posted_data[default_value_date_facet]" value="1" {if $attribute.default_values.facet}checked="true"{/if}/>
	<br><span class="attr_value_label">{$lng.lbl_description}</span>
	<textarea class="form-control" name="posted_data[default_value_date_description]" style="width: 500px">{$attribute.default_values.description|escape}</textarea>
	</div>
</div>
<!-- /Values -->
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_is_required}
    	<input type="hidden" name="posted_data[is_required]" value="0" />
    	<input type="checkbox" name="posted_data[is_required]" value="1" {if $attribute.is_required} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_sortable}
        <input type="hidden" name="posted_data[is_sortable]" value="0" />
    	<input type="checkbox" name="posted_data[is_sortable]" value="1" {if $attribute.is_sortable} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_comparable}
        <input type="hidden" name="posted_data[is_comparable]" value="0" />
    	<input type="checkbox" name="posted_data[is_comparable]" value="1" {if $attribute.is_comparable} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_is_active}
        <input type="hidden" name="posted_data[active]" value="0" />
    	<input type="checkbox" name="posted_data[active]" value="1" {if $attribute.active} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_show_for_customer}
        <input type="hidden" name="posted_data[is_show]" value="0" />
    	<input type="checkbox" name="posted_data[is_show]" value="1" {if $attribute.is_show} checked{/if} />
    </label>
</div>

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('attribute_modify_form')" style="btn-green push-20 push-5-r"}
</form>

</div>

<div class="box">

{include file='admin/attributes/product-filter.tpl'}

</div>

<div class="box">
{if in_array($attribute.pf_display_type, array('P','E','W','G'))}
{include file='common/subheader.tpl' title=$lng.lbl_images}

<form action="index.php?target={$current_target}&mode=att" method="post" name="upload_form">
<input type="hidden" name="action" value="images" />
<input type="hidden" name="attribute_id" value="{$attribute.attribute_id}">

<table class="table table-striped dataTable vertical-center" >
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    <th width="15">&nbsp;</th>
    <th width="115">{$lng.lbl_image}</th>
    <th width="115">{$lng.lbl_filename}</th>
    <th width="5%">{$lng.lbl_pos}</th>
</tr>
</thead>
{if $images}
{foreach from=$images item=image}
<tr{cycle values=", class='cycle'"}>
{if $ge_id}<td><input type="checkbox" value="Y" name="fields[d_image][{$image.image_id}]" /></td>{/if}
    <td><input type="checkbox" value="Y" name="iids[{$image.image_id}]" /></td>
    <td align="center"><a href="{$image.tmbn_url}" target="_blank"><img src="{$image.tmbn_url}" width="100" alt="" /></a></td>
    <td>{$image.filename}</td>
    <td><input type="text" size="5" maxlength="5" name="image[{$image.image_id}][orderby]" value="{$image.orderby}" /></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="7" align="center">{$lng.txt_no_images}</td>
</tr>
{/if}
</table>


{if $images}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('upload_form', 'images_update');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('upload_form', 'images_delete');" style="btn-danger push-20 push-5-r"}
{/if}

<div class="change_img">{include file='admin/images/edit.tpl' image='' button_name=$lng.lbl_browse in_type='attributes_images' idtag='edit_attributes_images'}</div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_upload href="javascript: cw_submit_form('upload_form');" style="btn-green push-20 push-5-r"}
</div>

</form>

{/if}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$lng.lbl_features_modify}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_expl_text_for_features}
