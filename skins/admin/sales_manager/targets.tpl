{capture name=section}
<form action="index.php?target=targets&user={$user}" method="post" name="targets_frm">
<input type="hidden" name="action" value="update_target">
<input type="hidden" name="user" value="{$user}">
<div class="input_field_0">
    <label>{$lng.lbl_current_level}</label>
    {include file='common/currency.tpl' value=$current_level}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_target} ({$config.General.currency_symbol})</label>
    <input type="text" name="posted_data[target]" value="{$salesman_target.target}" size="8">
</div>
<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
    {include file='main/select/date.tpl' name='posted_data[start_date]' value=$salesman_target.start_date}
    {include file='main/select/date.tpl' name='posted_data[end_date]' value=$salesman_target.end_date}
</div>
{if !$salesman_reached}
<input type="submit" value="{$lng.lbl_update}">
{/if}
</form>
{/capture}
{include file='common/section.tpl' title=$lng.lbl_target content=$smarty.capture.section}

{capture name=section}
{include file="main/select/edit_lng.tpl" script="`$navigation.script`&"}

<form action="index.php?target=targets&user={$user}" method="post" name="premium_form">
<input type="hidden" name="action" value="update_premiums">
<input type="hidden" name="user" value="{$user}">

<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_premium}</th>
    <th>{$lng.lbl_active}</th>
    <th>{$lng.lbl_pos}</th>
    <th>{$lng.lbl_customer_selection}</th>
</tr>
{if $premiums}
{foreach from=$premiums item=premium}
<tr>
    <td><input type="checkbox" name="data[{$premium.id}][del]" value="Y" /></td>
    <td>
        {include file="main/textarea.tpl" name="data[`$premium.id`][title]" cols=45 rows=8 class="InputWidth" data=$premium.title width="80%" btn_rows=4}
    </td>
    <td>
        <input type="hidden" name="data[{$premium.id}][active]" value="0" />
        <input type="checkbox" name="data[{$premium.id}][active]" value="1" {if $premium.active}checked{/if}/>
    </td>
    <td><input type="text" name="data[{$premium.id}][orderby]" value="{$premium.orderby}" size="3" /></td>
    <td>{if $premium.selected}{$lng.lbl_yes}{/if}</td>
</tr>
{/foreach}
<tr><td colspan="6">
    {include file='buttons/button.tpl' href="javascript: cw_submit_form('premium_form');" button_title=$lng.lbl_update_delete acl='__1112'}
</td></tr>
{else}
<tr>
    <td colspan="6" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr><td colspan="6">&nbsp;</td></tr>

{if $accl.__1112}
<tr><td colspan="6">{include file="common/subheader.tpl" title=$lng.lbl_add_new}</td></tr>
<tr>
    <td>&nbsp;</td>
    <td>
        {include file="main/textarea.tpl" name="data_new[title]" cols=45 rows=8 class="InputWidth" data='' width="80%" btn_rows=4}
    </td>
    <td><input type="checkbox" name="data_new[active]" value="1" /></td>
    <td><input type="text" name="data_new[orderby]" value="" size="3" /></td>
</tr>
{/if}
</table>
{include file='buttons/button.tpl' href="javascript: cw_submit_form('premium_form', 'add');" button_title=$lng.lbl_add acl='__1112'}

</form>
{/capture}

{capture name=dialog_sel}
<form action="index.php?target=targets&user={$user}" method="post" name="premium_form">
<input type="hidden" name="action" value="approve_premiums">
<input type="hidden" name="user" value="{$user}">

<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_premium}</th>
    <th>{$lng.lbl_customer_selection}</th>
</tr>
{if $premiums}
{foreach from=$premiums item=premium}
<tr>
    <td>{$premium.title}</td>
    <td width="1%"><input type="checkbox" name="data[{$premium.id}][selected]" value="1" {if $premium.selected}checked{/if} {if $salesman_target.approved}disabled{/if} /></td>
</tr>
{/foreach}
</table>
{if !$salesman_target.approved}
{include file='buttons/button.tpl' href="javascript:cw_submit_form('premium_form');" button_title=$lng.lbl_update acl='__1112'}
{/if}
{/if}
</form>
{/capture}

{if $premiums_selected}
{include file='common/section.tpl' title=$lng.lbl_premiums content=$smarty.capture.section_sel}
{elseif !$salesman_reached}
{include file='common/section.tpl' title=$lng.lbl_premiums content=$smarty.capture.section}
{/if}
