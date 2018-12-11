<script language="javascript">
{literal}
function visibleTA(obj) {
    var objTA = document.getElementById('product_options_list');
    if (obj != null && objTA != null) {
        if (obj.options[obj.selectedIndex].value == 'T') { 
            objTA.style.display = 'none';
        } else {
            objTA.style.display = '';
        } 
    }
/*
    if (obj != null && objTA != null) {
        objTA.disabled = (obj.options[obj.selectedIndex].value == 'T');
    }
*/
    var objTT = document.getElementById('product_options_text_type');
    if (obj != null && objTT != null) {
        if (obj.options[obj.selectedIndex].value == 'T') { 
            objTT.style.display = '';
        } else {
            objTT.style.display = 'none';
        } 
    }

    var objTL = document.getElementById('product_options_text_limit');
    if (obj != null && objTL != null) {
        if (obj.options[obj.selectedIndex].value == 'T') { 
            objTL.style.display = '';
        } else {
            objTL.style.display = 'none';
        } 
    }

    var objTL = document.getElementById('product_options_allowed_with_variants');
    if (obj != null && objTL != null) {
        if (obj.options[obj.selectedIndex].value == 'T') { 
            objTL.style.display = '';
        } else {
            objTL.style.display = 'none';
        } 
    }


}
{/literal}
-->
</script>

{if !$script_name}{assign var="script_name" value="index.php?target=`$current_target`&mode=details&js_tab=product_options&product_id=`$product.product_id``$redirect_ge_id`"}{/if}

{capture name=section}
{if $product_options}
{include file='admin/buttons/button.tpl' href=$script_name button_title=$lng.lbl_back_to_option_groups_list style="push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="`$script_name`&submode=product_options_add" button_title=$lng.lbl_add_option acl=$page_acl style="btn-green push-20"}

{include file='main/select/edit_lng.tpl' script="`$script_name`&product_option_id=`$product_option.product_option_id`"}
{/if}

<form action="{$script_name}" method="post" name="option_form" class="form-horizontal">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="product_options_add" />
<input type="hidden" name="product_option_id" value="{$product_option.product_option_id}" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[option_field]" />{/if}
    {$lng.lbl_option_group_name}
    </label>
    <div class="col-xs-12"><input class="form-control" type="text" size="50" maxlength="128" name="add[field]" value="{$product_option.field}" /></div>
    <div class="col-xs-12">{$lng.txt_option_group_name_note}</div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[option_name]" />{/if}
    {$lng.lbl_option_text}
    </label>
    <div class="col-xs-12"><input class="form-control" type="text" size="50" maxlength="255" name="add[name]" value="{$product_option.name}" /></div>
    <div class="col-xs-12">{$lng.txt_option_group_comment_note}</div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[option_type]" />{/if}
    {$lng.lbl_option_group_type}
    </label>
    <div class="col-xs-12">{include file='addons/product_options/main/select/option-type.tpl' name='add[type]' value=$product_option.type onchange='visibleTA(this);' id='product_option_type'}</div>

</div>
<div class="form-group" id="product_options_text_type">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[text_type]" />{/if}
    {$lng.lbl_text_type}
    </label>
    <div class="col-xs-12">
    	<select name="add[text_type]">
    	<option value="">{$lng.lbl_text_input}</option> 
    	<option value="A" {if $product_option.text_type eq 'A'}selected="selected"{/if}>{$lng.lbl_text_area}</option>
    	</select>
    </div>
</div>
<div class="form-group" id="product_options_text_limit">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[text_limit]" />{/if}
    {$lng.lbl_text_limit}
    </label>
    <div class="col-xs-12"><input class="form-control" type="text" size="5" maxlength="11" name="add[text_limit]" value="{$product_option.text_limit|default:100}" /></div>
</div>
{include file='addons/product_options/main/product_option_extra_params.tpl' product_option=$product_option}
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[orderby]" />{/if}
    {$lng.lbl_orderby}
    </label>
    <div class="col-xs-12"><input class="form-control" type="text" size="5" maxlength="11" name="add[orderby]" value="{$product_option.orderby}" /></div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[avail]" />{/if}
    {$lng.lbl_availability}
    </label>
    <div class="col-xs-12">{include file='admin/select/availability.tpl' name='add[avail]' value=$product_option.avail}</div>
</div>
<div id="product_options_list">
    <label class="col-xs-12">
    {if $ge_id && $product_option}<input type="checkbox" value="1" name="fields[options]" />{/if}
    {include file="common/subheader.tpl" title=$lng.lbl_options_list}
    </label>
    {if $product_option.type ne 'T'}
    <table class="table table-striped dataTable vertical-center">
    <thead>
    <tr>
        <th width="10">&nbsp;</th>
        <th>{$lng.lbl_option_value}</th>
        <th>{$lng.lbl_orderby}</th>
        <th>{$lng.lbl_availability}</th>
{if $product_option.type eq 'Y'}
        <th colspan="2">{$lng.lbl_option_surcharge}</th>
        <th colspan="2">{$lng.lbl_cost_surcharge}</th>
{/if}
		<th>&nbsp;</th>
    </tr>
    </thead>
    {foreach from=$product_option.options item=o}
    <tr{cycle name="options" values=', class="cycle"'}>
        <td><input type="checkbox" name="to_delete[{$o.option_id}]" value="Y" /></td>
        <td><input class="form-control" type="text" name="list[{$o.option_id}][name]" value="{$o.name|escape}" /></td>
        <td><input class="form-control" type="text" name="list[{$o.option_id}][orderby]" size="5" maxlength="11" value="{$o.orderby}" /></td>
        <td>{include file='admin/select/availability.tpl' name="list[`$o.option_id`][avail]" value=$o.avail}</td>
{if $product_option.type eq 'Y'}
        <td><input class="form-control" type="text" name="list[{$o.option_id}][price_modifier]" size="5" value="{$o.price_modifier|formatprice}" /></td>
        <td>{include file='main/select/modifier_type.tpl' name="list[`$o.option_id`][modifier_type]" value=$o.modifier_type}</td>
        <td><input class="form-control" type="text" name="list[{$o.option_id}][cost_modifier]" size="5" value="{$o.cost_modifier|formatprice}" /></td>
        <td>{include file='main/select/modifier_type.tpl' name="list[`$o.option_id`][cost_modifier_type]" value=$o.cost_modifier_type}</td>
{/if}
		<td>&nbsp;</td>
    </tr>
    {/foreach}
    {if $product_option.options}
    <tr>
        <td colspan="{if $product_option.type eq 'Y'}9{else}5{/if}">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('option_form', 'product_option_delete')" acl=$page_acl style="btn-danger"}</td>
    </tr>
    {else}
        <td colspan="{if $product_option.type eq 'Y'}9{else}5{/if}" class="text-center">{$lng.lbl_no_options}</td>
    {/if}
{if $accl.$page_acl}
    <tr>
        <td colspan="{if $product_option.type eq 'Y'}9{else}5{/if}">{include file="common/subheader.tpl" title=$lng.lbl_add_option_value}</td>
    </tr>
    <tr align="center">
        <td align="center" id="popt_box_1">&nbsp;</td>
        <td align="center" id="popt_box_2"><input class="form-control" type="text" name="new_list[0][name]" /></td>
        <td align="center" id="popt_box_3"><input class="form-control" type="text" name="new_list[0][orderby]" size="5" maxlength="11" /></td>
        <td align="center" id="popt_box_1">{include file='admin/select/availability.tpl' name='new_list[0][avail]' value=1}</td>
{if $product_option.type eq 'Y'}
        <td align="right" id="popt_box_4"><input class="form-control" type="text" name="new_list[0][price_modifier]" size="5" value="{$zero}" /></td>
        <td align="left" id="popt_box_5">{include file='main/select/modifier_type.tpl' name="new_list[0][modifier_type]" value=0}</td>
        <td align="right" id="popt_box_4"><input class="form-control" type="text" name="new_list[0][cost_modifier]" size="5" value="{$zero}" /></td>
        <td align="left" id="popt_box_5">{include file='main/select/modifier_type.tpl' name="new_list[0][cost_modifier_type]" value=0}</td>
{/if}
        <td align="center">{include file='main/multirow_add.tpl' mark='popt' is_lined=true}</td>
    </tr>
    <tr>
        <td colspan="{if $product_option.type eq 'Y'}9{else}5{/if}">
          <table width="100%"> 
           <tr> 
            <td width="100%">
            <label>Options as text:</label>
            <textarea class="form-control" name="new_list_as_text" rows="5" style="width:95%"></textarea></td>
           </tr>
          </table>  
        </td>
    </tr>
{/if}
    </table>
    {elseif $product_option.type eq 'T'}
    <font color="red">{$lng.txt_text_field_note}</font>
    {/if}
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('option_form')" acl=$page_acl style="btn-green push-20"}
</div>
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_update_option content=$smarty.capture.section}
<script language="javascript">
<!--
visibleTA(document.getElementById('product_option_type')); 
-->
</script>
