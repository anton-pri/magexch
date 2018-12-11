{include file="addons/flexible_import/flexible_import_menu.tpl"}

{capture name=section}
{capture name=block1}

{if $dup_items ne ''}
<form name="check_dup_items_form" method="get" action="index.php?target={$current_target}">
<input type="hidden" name="target" value="{$current_target}">
<input type="hidden" name="action" value="" />
<input type="hidden" name="step12passed" value="Y"/>
<table width='100%' cellpadding='3' cellspacing='0' class="table table-striped">
<thead>
  <tr>
    <th>ALU</th> 
    <th nowrap>Item Number</th>
    <th width="20%">Description</th>  
    <th>X-REF</th>
    <th>ALU</th>               
    <th nowrap>Item Number</th>
    <th width="20%">Description</th>
    <th>X-REF</th>
  </tr>
</thead>
{foreach from=$dup_items item=di}
<tr>
    <td>{if $prev_xref ne $di.o_XREF}{$di.o_ALU}{/if}&nbsp;</td>
    <td>{if $prev_xref ne $di.o_XREF}{$di.o_ITEMNUMBER}{/if}&nbsp;</td>
    <td>{if $prev_xref ne $di.o_XREF}{$di.o_DESC1}{/if}&nbsp;</td>
    <td nowrap>{if $prev_xref ne $di.o_XREF}{$di.o_XREF}{/if}&nbsp;</td>
    <td>{$di.d_ALU}</td>
    <td>{$di.d_ITEMNUMBER}</td>
    <td>{$di.d_DESC1}</td>
    <td nowrap>{$di.d_XREF}</td>
</tr>
{assign var='prev_xref' value=$di.o_XREF}
{/foreach}
</table>
<div>
<br><br>
{include file='buttons/button.tpl' button_title=$lng.lbl_proceed|default:'Proceed anyway with export' href="javascript:cw_submit_form('check_dup_items_form', 'proceed');" style='btn-green'}
&nbsp;&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_refresh|default:'Referesh page' href="javascript:cw_submit_form('check_dup_items_form', 'refresh');" style='btn-green'}
<br><br>
</div>
</form>
{else}
<form name="check_dup_items_form" method="get" action="index.php?target={$current_target}">
<input type="hidden" name="target" value="{$current_target}">
<input type="hidden" name="action" value="" />
<input type="hidden" name="step12passed" value="Y"/>
<h4>No duplicate xref items found.</h4><br><br>
{include file='buttons/button.tpl' button_title=$lng.lbl_proceed|default:'Proceed with export' href="javascript:cw_submit_form('check_dup_items_form', 'proceed');" style='btn-green'}
<br><br>
</form>
{/if}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_duplicate_items_list|default:'Duplicate Items List'}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_duplicate_items_check|default:'Duplicate items check'}
