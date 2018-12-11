{include file="addons/flexible_import/flexible_import_menu.tpl" active="6"}

{capture name=section}
{capture name=block1}
<form name="buffer_match_cfg_form" method="post" action="index.php?target=datahub_configuration">
<input type="hidden" name="action" value="save_match_config" />

<table width="95%">
<thead>
  <tr>
    <th>Main Table Columns</th> 
    <th>Buffer Table Columns</th>
    <th width="60%">Custom SQL</th>  
    <th>Update Conditions</th>
  </tr>
</thead>
{foreach from=$main_tbl_fields item=mfield}
<tr>
   <td valign="top">{$mfield}</td>
   {if $mfield eq 'ID'}<td colspan="3" style="height:30px" valign="top" align="center"><i>Datahub table key field, auto generated</i></td>{else}
   <td valign="top">
     <select name="buffer_match[{$mfield}][bfield]">
       <option value=''></option>
       {foreach from=$buffer_tbl_fields item=bfield} 
         <option value='{$bfield}' {if $bm_config.$mfield.bfield eq $bfield}selected="selected"{/if}>{$bfield}</option>
       {/foreach}
     </select>
   </td>
   <td valign="top" align="center"><textarea name="buffer_match[{$mfield}][custom_sql]" rows="3" style="width:95%">{$bm_config.$mfield.custom_sql}</textarea></td>
   <td valign="top">
     <select name="buffer_match[{$mfield}][update_cond]">
       {foreach from=$update_cond_options item=uc_title key=uc_code}
         <option value='{$uc_code}' {if $bm_config.$mfield.update_cond eq $uc_code}selected="selected"{/if}>{$uc_title}</option>
       {/foreach}
     </select>
   </td>
   {/if}
</tr>
{/foreach}
</table>
<div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('buffer_match_cfg_form');" style='btn-green'}
<br><br>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_buffer_match_settings|default:'Buffer Match Settings'}


{capture name=block1_1}
<form name="buffer_update_nonstock_cfg_form" method="post" action="index.php?target=datahub_configuration">
<input type="hidden" name="action" value="save_update_nonstock_config" />
<table width="65%" id="buff_update_nonstock">
<thead>
  <tr>
    <th>Buffer Table Columns</th>
    <th>Main Table Columns</th>
    <th>Update Conditions</th>
  </tr>
</thead>
{foreach from=$buffer_tbl_fields item=bfield}
<tr>
   <td valign="top">{$bfield}</td>
   {if $bfield eq 'table_id' || $bfield eq 'item_xref'}<td colspan="3" style="height:30px" valign="top" align="center"><i>Buffer table key field, auto generated</i></td>{else}
   <td valign="top">
     <select name="buffer_update_nonstock[{$bfield}][mfield]" style="width:100%">
       <option value=''></option>
       {foreach from=$main_tbl_fields item=mfield}
         <option value='{$mfield}' {if $update_nonstock_config.$bfield.mfield eq $mfield}selected="selected"{/if}>{$mfield}</option>
       {/foreach}
     </select>
   </td>
   <td valign="top" align='center'>
     <select name="buffer_update_nonstock[{$bfield}][update_nonstock_cond]">
       {foreach from=$update_cond_options item=uc_title key=uc_code}
         <option value='{$uc_code}' {if $update_nonstock_config.$bfield.update_nonstock_cond eq $uc_code}selected="selected"{/if}>{$uc_title}</option>
       {/foreach}
     </select>
   </td>
   {/if}
</tr>
{/foreach}
</table>
<div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('buffer_update_nonstock_cfg_form');" style='btn-green'}
<br><br>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1_1 title=$lng.lbl_buffer_update_nonstock_settings|default:'Buffer Item Update Non-stock data Settings'}


{capture name=block2}
<form name="buffer_merge_cfg_form" method="post" action="index.php?target=datahub_configuration">
<input type="hidden" name="action" value="save_merge_config" />
<table width="65%" id="buff_merge">
<thead>
  <tr>
    <th>Buffer Table Columns</th>
    <th>Main Table Columns</th>
    <th>Automatic Merge Conditions</th>
  </tr>
</thead>
{foreach from=$buffer_tbl_fields item=bfield}
<tr>
   <td valign="top">{$bfield}</td>
   {if $bfield eq 'table_id' || $bfield eq 'item_xref'}<td colspan="3" style="height:30px" valign="top" align="center"><i>Buffer table key field, auto generated</i></td>{else}
   <td valign="top">
     <select name="buffer_merge[{$bfield}][mfield]" style="width:100%">
       <option value=''></option>
       {foreach from=$main_tbl_fields item=mfield}
         <option value='{$mfield}' {if $bmerge_config.$bfield.mfield eq $mfield}selected="selected"{/if}>{$mfield}</option>
       {/foreach}
     </select>
   </td>
   <td valign="top" align='center'>
     <select name="buffer_merge[{$bfield}][merge_cond]">
       {foreach from=$merge_cond_options item=mc_title key=mc_code}
         <option value='{$mc_code}' {if $bmerge_config.$bfield.merge_cond eq $mc_code}selected="selected"{/if}>{$mc_title}</option>
       {/foreach}
     </select>
   </td>
   {/if}
</tr>
{/foreach}
</table>
<div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('buffer_merge_cfg_form');" style='btn-green'}
<br><br>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_buffer_merge_settings|default:'Buffer Item Merge Settings'}

{capture name=block3}
<form name="price_settings_cfg_form" method="post" action="index.php?target=datahub_configuration">
<input type="hidden" name="action" value="save_price_config" />
<table width="45%" id="price_config">
<thead>
  <tr>
    <th>Setting Name</th>
    <th>Value</th>
  </tr>
</thead>
{foreach from=$price_settings key=fname item=val}
<tr {if $val.hide}style="display:none;"{/if}>
<td title="{$fname}">{$val.title|default:$fname|capitalize|replace:'_':' '}<br><i>{$val.comment}</i></td>
<td><input type="text" style="width:95%" name="price_settings[{$fname}]" value="{$val.value}" /></td>
</tr>
{/foreach}
</table>
<div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('price_settings_cfg_form');" style='btn-green'}
<br><br>
</div>
</form>
{include file='buttons/button.tpl' button_title=$lng.lbl_reset_main_data_fingerprint|default:'Reset Main data fingerprint' href="index.php?target=datahub_reset_transfer_live" style='btn-danger'}
<br><br>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block3 title=$lng.lbl_price_update_settings|default:'Price Update Settings'}

{capture name=block4}
<form name="pos_update_cfg_form" method="post" action="index.php?target=datahub_configuration">
<input type="hidden" name="action" value="save_pos_config" />

<table width="95%" id="pos_update">
<thead>
  <tr>
    <th>Main Table Columns</th>
    <th>POS Table Columns</th>
    <th width="60%">Custom SQL</th>
    <th>Update Conditions</th>
  </tr>
</thead>
{foreach from=$main_tbl_fields item=mfield}
<tr>
   <td valign="top">{$mfield}</td>
   {if $mfield eq 'ID'}<td colspan="3" style="height:30px" valign="top" align="center"><i>Datahub table key field, auto generated</i></td>{else}
   <td valign="top">
     <select name="pos_update[{$mfield}][pfield]">
       <option value=''></option>
       {foreach from=$pos_tbl_fields item=pfield}
         <option value='{$pfield}' {if $pos_config.$mfield.pfield eq $pfield}selected="selected"{/if}>{$pfield}</option>
       {/foreach}
     </select>
   </td>
   <td valign="top" align="center"><textarea name="pos_update[{$mfield}][custom_sql]" rows="3" style="width:95%">{$bm_config.$mfield.custom_sql}</textarea></td>
   <td valign="top">
     <select name="pos_update[{$mfield}][update_cond]">
       {foreach from=$update_cond_options item=uc_title key=uc_code}
         <option value='{$uc_code}' {if $pos_config.$mfield.update_cond eq $uc_code}selected="selected"{/if}>{$uc_title}</option>
       {/foreach}
     </select>
   </td>
   {/if}
</tr>
{/foreach}
</table>
<div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('pos_update_cfg_form');" style='btn-green'}
<br><br>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block4 title=$lng.lbl_pos_update_settings|default:'POS Update Settings'}


{/capture}

{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_datahub_configuration|default:'DataHub Configuration'}


