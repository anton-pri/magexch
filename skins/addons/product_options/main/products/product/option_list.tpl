{if !$script_name}{assign var="script_name" value="index.php?target=`$current_target`"}{/if}

{capture name=section}
<div class="dialog_title">{$lng.txt_product_options_list_note}</div>

{if $product_options}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var v_alert = "{$lng.txt_variant_alert|escape:javascript|replace:'"':'\"'|replace:"\n":""}";
var v_del_alert = "{$lng.txt_delete_variant_alert|escape:javascript|replace:'"':'\"'|replace:"\n":""}";
var del_variants = [];
var disabled_variants = [];
{foreach from=$product_options item=v key=k}
{if $v.type eq ''}
del_variants[{$v.product_option_id}] = true;
{if !$v.avail}
disabled_variants[{$v.product_option_id}] = true;
{/if}
{/if}
{/foreach}

{literal}
function variant_alert(obj, id) {
    if(!obj)
        return false
    if(!obj.checked && !disabled_variants[id])
        return confirm(v_alert);
    return true;
}

function variant_del_alert() {
    if (del_variants.length == 0)
        return true;

    for (var x in del_variants) {
        if (isNaN(x))
            continue;
        var n = document.product_options_form.elements['to_delete['+x+']'];
        if (n && n.checked)
            return confirm(v_del_alert);
    }
    return true;
}

{/literal}
-->
</script>

{/if}

<form action="{$script_name}" method="post" name="product_options_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="section" value="options" />
<input type="hidden" name="action" value="product_options_modify" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}
<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}
{/if}
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    <th width="10"><input type='checkbox' class='select_all' class_to_select='product_options_item' /></th>
    <th>{$lng.lbl_option_class}</th>
    <th>{$lng.lbl_option_type}</th>
    <th>{$lng.lbl_orderby}</th>
    <th class="text-center">{$lng.lbl_availability}</th>
    <th width="40%">{$lng.lbl_options_list}</th>
</tr>
</thead>

{foreach from=$product_options item=v}
<tr{cycle name="classes" values=', class="cycle"'} valign="top">
{if $ge_id}<td><input type="checkbox" value="1" name="fields[classes][{$v.product_option_id}]" /></td>{/if}
    <td><input type="checkbox" name="to_delete[{$v.product_option_id}]" value="Y" class="product_options_item" /></td>
    <td><a href="{$script_name}&amp;mode=details&amp;js_tab=product_options&product_id={$product.product_id}&amp;product_option_id={$v.product_option_id}{$redirect_ge_id}">{$v.field} / {$v.name}</a></td>
    <td>{include file='addons/product_options/main/select/option-type.tpl' value=$v.type is_text=1}</td>
    <td><input type="text" name="po_classes[{$v.product_option_id}][orderby]" size="5" maxlength="11" value="{$v.orderby}" /></td>
    <td align="center">
        <input type="checkbox" name="po_classes[{$v.product_option_id}][avail]" value="1"{if $v.avail} checked="checked"{/if}{if $v.type eq ''} onclick="javascript: return variant_alert(this, {$v.product_option_id});"{/if} />
    </td>
    <td valign="top"><table cellspacing="0" cellpadding="2">
    {foreach from=$v.options item=o}
    <tr>
        <td>{if !$o.avail}<font color="red">{/if}{$o.name}{if !$o.avail}</font>{/if}</td>
    {if $v.type eq 'Y' && $o.price_modifier ne 0}
        <td>{$o.price_modifier|formatprice}</td>
        <td>{if $o.modifier_type|default:"$" eq '$'}{$config.General.currency_symbol}{else}%{/if}</td>
    {/if}
    </tr>
    {foreachelse}
    <tr>
    {if $ge_id}<td>&nbsp;</td>{/if}
    <td colspan="{if $v.type eq 'Y'}3{else}1{/if}">{$lng.lbl_options_list_empty}</td>
    </tr>
    {/foreach}</table>
    </td>
</tr>
{foreachelse}
<tr>
    <td align="center" colspan="8">{$lng.lbl_product_options_list_empty}</td>
</tr>
{/foreach}
</table>
{if $product_options}

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form(document.product_options_form)" style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected  onclick="javascript: if(variant_del_alert()) cw_submit_form(document.product_options_form, 'product_options_delete');" style="btn-danger push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="`$script_name`&mode=details&submode=product_options_add&product_id=`$product.product_id`&`$redirect_ge_id`&amp;js_tab=product_options" style="btn-green push-5-r push-20"}

{/if}
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_product_option_groups content=$smarty.capture.section}

{if $product_options}
<a name="exceptions"></a>
{capture name=section2}
<div class="dialog_title">{$lng.txt_product_option_exceptions_note}</div>

{if $def_options_failure}
<div class="field_error">{$lng.lbl_warning}: {$lng.txt_default_options_failure_note}</div>
{/if}
<form action="{$script_name}" method="post" name="frm_exception">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="section" value="options" />
<input type="hidden" name="action" value="products_options_ex_add" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    <th width="10">&nbsp;</th>
    <th>{$lng.lbl_options_combination}</th>
</tr>
</thead>
{foreach from=$products_options_ex key=k item=o}
<tr{cycle name="exceptions" values=', class="cycle"'}>
    {if $ge_id}<td width="15" class="TableSubHead" rowspan=""><input type="checkbox" value="1" name="fields[exceptions][{$o.exception_id}]" /></td>{/if}
    <td width="10"><input type="checkbox" name="to_delete[{$k}]" /></td>
    <td>{foreach from=$o item=v}
        <span style="white-space: nowrap;">{$v.option_name}:&nbsp;
        {foreach from=$product_options item=c}
        {if $c.product_option_id eq $v.product_option_id}
            {foreach from=$c.options item=o}
            {if $o.option_id eq $v.option_id}{$o.name}{/if}
            {/foreach}
        {/if}
        {/foreach}
        </span>&nbsp;&nbsp;
    {/foreach}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="3" align="center">{$lng.lbl_exceptions_list_empty}</td>
</tr>
{/foreach}
{if $products_options_ex}
<tr>
    {if $ge_id}<td width="15">&nbsp;</td>{/if}
    <td colspan="2"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: cw_submit_form('frm_exception', 'products_options_ex_delete');" /></td>
</tr>
{/if}
<thead>
<tr>
    <td colspan="3">{$lng.lbl_add_exception}</td>
</tr>
</thead>
<tr>
    {if $ge_id}<td><input type="checkbox" value="1" name="fields[new_exception]" /></td>{/if}
    <td colspan="2" class="form-horizontal">
    {foreach from=$product_options item=v}
    {if $v.options}
    <div class="form-group">
        <label class="col-xs-12">{$v.name}</label>
        <div class="col-xs-12">
       			<select class="form-control" name="new_exception[{$v.product_option_id}]" style="width:auto;">
        			<option value="">{$lng.lbl_select_one_bracket}</option>
        			{foreach from=$v.options item=o}
        			<option value='{$o.option_id}'>{$o.name}</option>
        			{/foreach}
        		</select>
   		</div>
    {/if}
    {/foreach}
    </td>
</tr>
</table>
<input type="submit" value="{$lng.lbl_add_exception|strip_tags:false|escape}" class="btn btn-green push-20"/>
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_product_option_exceptions content=$smarty.capture.section2}

<a name="js_code"></a>
{capture name=section3}
{if $config.General.display_all_products_on_1_page eq 'Y'}<div align="right"><a href="#main">{$lng.lbl_top}</a></div>{/if}
<p>{$lng.txt_product_options_js_note}</p>

<form action="{$script_name}" method="post" name="validateform" class="form-horizontal">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="section" value="options" />
<input type="hidden" name="action" value="product_options_js_update" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}
<div class="form-group">
    <label class="col-xs-12">
    {if $ge_id}<input type="checkbox" value="1" name="fields[js]" />{/if}
    {$lng.lbl_validation_script_javascript}
    </label>
    <div class="col-xs-12"><textarea name="js_code" cols="60" rows="15" class="form-control">{$product_options_js}</textarea></div>
</div>
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" class="btn btn-green push-20" />
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_validation_script_javascript content=$smarty.capture.section3}

{/if}
