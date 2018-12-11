<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
$(document).ready(function() {
  $(".ps_default_status").on("click", function() {
    var checkbox_id = $(this).attr('name');
    checkbox_id = checkbox_id.replace('default_status[',''); 
    checkbox_id = checkbox_id.replace(']',''); 
    $("#stage_status_"+checkbox_id).toggle(!this.checked);
    $("#stage_default_status_"+checkbox_id).toggle(this.checked);
  });
}
);
{/literal}
-->
</script>

<form action="{$script_name}" method="post" name="product_stages_form">
{capture name=section}
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="section" value="product_stages" />
<input type="hidden" name="action" value="product_stages_modify" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
{if $product_stages}
<table class="header" width="100%">
<tr>
    <th width="10"><input type='checkbox' class='select_all' class_to_select='product_stages_item' /></th>
    <th width="25%" align="center">{$lng.lbl_name}</th>
    <th width="30%" align="center">{$lng.lbl_product_stages_period}</th>
    <th width="30%" align="center">{$lng.lbl_status}</th>
    <th width="15%" align="center">{$lng.lbl_active}</th>
</tr>
{foreach from=$product_stages item=ps}
<tr{cycle name="classes" values=', class="cycle"'} valign="top">
   <td align="center" valign="top"><input type="checkbox" name="to_delete[{$ps.setting_id}]" value="Y" class="product_stages_item" /></td>
   <td valign="top" style="padding-top: 22px;">{$ps.title}</td>
   <td valign="top">
       <select name="posted_data[{$ps.setting_id}][period]">
       <option value="-1" {if $ps.period eq -1}selected="selected"{/if}>{$lng.lbl_default} ({$ps.default_period} days)</option>
       {section name=period start=1 loop=60 step=1}
       <option value="{$smarty.section.period.index}" {if $smarty.section.period.index eq $ps.period}selected="selected"{/if}>{$smarty.section.period.index}</option>
       {/section}
       </select>
   </td>
   <td align="center" valign="top">
       {$lng.lbl_default}:&nbsp;<input type="checkbox" class="ps_default_status" name="default_status[{$ps.setting_id}]" value="1" {if $ps.status eq -1}checked="checked"{/if} /><br />
       <div {if $ps.status ne -1}style="display:none;"{/if} id="stage_default_status_{$ps.setting_id}">&nbsp;{foreach from=$ps.default_status item=ds name=dslist}{include file="main/select/doc_status.tpl" status=$ds mode="static"}{if !$smarty.foreach.dslist.last}<br /> {/if}{/foreach}</div>
       <div {if $ps.status eq -1}style="display:none;"{/if} id="stage_status_{$ps.setting_id}">
       {if $ps.status ne -1} 
       {include file="main/select/doc_status.tpl" status=$ps.status normal_array="1" name="posted_data[`$ps.setting_id`][status][]" mode="select" multiple="1"}
       {else} 
       {include file="main/select/doc_status.tpl" status=$ps.default_status normal_array="1" name="posted_data[`$ps.setting_id`][status][]" mode="select" multiple="1"} 
       {/if}
       </div>
   </td>
   <td align="center" valign="top">
       <input type="checkbox" name="posted_data[{$ps.setting_id}][active]" value="1" {if $ps.active eq 1}checked="checked"{/if} />
   </td>
</tr>
{/foreach}
</table>

<div class="buttons bottom">
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form(document.product_stages_form)"}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected  onclick="javascript: cw_submit_form(document.product_stages_form, 'product_stages_delete');"}
</div>
{else}
<div class="dialog_title">{$lng.txt_no_product_stages_defined|default:'No Order Stages Defined'}</div>
{/if}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_product_stages content=$smarty.capture.section}


{capture name=section}

<table class="header" width="100%">
<tr>
    <th width="10">&nbsp;</th>
    <th width="25%" align="center">{$lng.lbl_name}</th>
    <th width="30%" align="center">{$lng.lbl_product_stages_period}</th>
    <th width="30%" align="center">{$lng.lbl_status}</th>
    <th width="15%" align="center">{$lng.lbl_active}</th>
</tr>
<tr valign="top">
   <td align="center" valign="top">&nbsp;</td>
   <td valign="top">
   <select name="new_product_stage[stage_lib_id]">
   {foreach from=$lib_stages item=ls}
   <option value="{$ls.stage_lib_id}">{$ls.title}</option>
   {/foreach}
   </select>
   </td>
   <td valign="top">
       <select name="new_product_stage[period]">
         <option value="-1" selected="selected">{$lng.lbl_default}</option>
         {section name=period start=1 loop=60 step=1}
         <option value="{$smarty.section.period.index}">{$smarty.section.period.index}</option>
         {/section}
       </select>
   </td>
   <td align="center" valign="top">
{$lng.lbl_default}:&nbsp;<input type="checkbox" class="ps_default_status" name="default_status[new_product_stage]" value="1" checked="checked" /><br />
       <div style="display:none;" id="stage_status_new_product_stage">
       {include file="main/select/doc_status.tpl" normal_array="1" name="new_product_stage[status][]" mode="select" multiple="1"}
       </div>
   </td>
   <td align="center" valign="top">
       <input type="checkbox" name="new_product_stage[active]" value="1" checked="checked" />
   </td>
</tr>
</table>
<br />
<div class="buttons bottom">
{include file='buttons/button.tpl' button_title=$lng.lbl_add  onclick="javascript: cw_submit_form(document.product_stages_form, 'product_stages_add');"}
</div>
{/capture}
{include file='common/section.tpl' title="Add Stage Setting" content=$smarty.capture.section}

</form>
