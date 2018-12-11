{capture name=section}

<form method="post" action="index.php?target=memberships" name="form{$type}">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="add[area]" value="{$type}" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th  width="10">{$lng.lbl_delete}</td>
	<th >{$lng.lbl_membership}</th>
	<th >{$lng.lbl_active}</th>
	<th >{$lng.lbl_orderby}</th>
	<th nowrap="nowrap" align="center">{$lng.lbl_assigned_users}</th>
    {if $type eq 'C' || $type eq 'R'}
    <th  width="10">{$lng.lbl_settings}</th>
    {/if}
    {if $type eq 'B'}<th>{$lng.lbl_register}</th>{/if}
    {if $type eq 'A' || $type eq 'P' || $type eq 'G'}<th>{$lng.lbl_access}</th>{/if}
</tr>
</thead>
{if $type eq 'A' || $type eq 'P' || $type eq 'G'}
<tr{cycle name=$type values=", class='cycle'"}>
    <td>&nbsp;</td>
    <td>{$lng.lbl_master_admin}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center">{$no_membership[$type]}</td>
    <td align="center"><a href="index.php?target=access_level&mem_area={$type}&membership_id=0">{$lng.lbl_access_level}</a></td>
</tr>
{/if}
{foreach from=$levels item=v}
{assign var="membership_id" value=$v.membership_id}
<tr{cycle name=$type values=", class='cycle'"}>
{if $membership_id}
	<td align="center"><input type="checkbox" name="to_delete[]" value="{$membership_id}" /></td>
	<td><input type="text" size="30" name="posted_data[{$membership_id}][membership]" value="{$v.membership|escape}" class="form-control" /></td>
	<td align="center">
        <input type="hidden" name="posted_data[{$membership_id}][active]" value="N" />
        <input type="checkbox" name="posted_data[{$membership_id}][active]" value="Y"{if $v.active eq 'Y'} checked="checked"{/if} />
    </td>
	<td align="center"><input type="text" size="5" name="posted_data[{$membership_id}][orderby]" value="{$v.orderby}"  class="form-control"/></td>
{else}
    <td>&nbsp;</td>
    <td colspan="3">{$lng.lbl_retail}</td>
{/if}
	<td align="center">{$v.users|default:$lng.txt_not_available}</td>
    {if $type eq 'C' || $type eq 'R'}
    <td align="center">{include file="main/visiblebox_link.tpl" mark="membership_`$membership_id`" title=""}</td>
    {/if}
    {if $type eq 'B'}
    <td align="center">
        <input type="hidden" name="posted_data[{$membership_id}][show_on_register]" value="N" />
        <input type="checkbox" name="posted_data[{$membership_id}][show_on_register]" value="Y"{if $v.show_on_register eq 'Y'} checked="checked"{/if} />
    </td>
    {/if}
    {if $type eq 'A' || $type eq 'P' || $type eq 'G'}
    <td align="center"> 
        <a href="index.php?target=access_level&mem_area={$type}&membership_id={$membership_id}">{$lng.lbl_access_level}</a>
    </td>
    {/if}
</tr>
{if $type eq 'C' || $type eq 'R'}
<tr id="membership_{$membership_id}" style="display:none">
    <td colspan="6" style="padding: 0;">
<table class="table" width="100%">
<thead>
<tr>
    <th class="text-center">{$lng.lbl_default}</th>
    <th class="text-center">{$lng.lbl_show_summary}</th>
    <th class="text-center">{$lng.lbl_show_price}</th>
</tr>
</thead>
<tr>
    <td align="center">
{if $membership_id}
        <input type="hidden" name="posted_data[{$membership_id}][default_membership]" value="N" />
        <input type="checkbox" name="posted_data[{$membership_id}][default_membership]" value="Y"{if $v.default_membership eq 'Y'} checked="checked"{/if} />
{/if}
    </td>
    <td align="center">
        <input type="hidden" name="posted_data[{$membership_id}][show_summary]" value="N" />
        <input type="checkbox" name="posted_data[{$membership_id}][show_summary]" value="Y"{if $v.show_summary eq 'Y'} checked="checked"{/if} />
    </td>
    <td align="center">
        <input type="hidden" name="posted_data[{$membership_id}][show_prices]" value="0" />
        <input type="checkbox" name="posted_data[{$membership_id}][show_prices]" value="1"{if $v.show_prices} checked="checked"{/if} />
    </td>
</tr>
</table>
    </td>
</tr>
{/if}
{/foreach}

{if $levels}

{else}

<tr>
	<td colspan="{if $type eq 'A' || $type eq 'P'}6{else}5{/if}" align="center">{$lng.txt_no_memberships_defined}</td>
</tr>

{/if}

</table>
    <div class="buttons">
    {if $levels}
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form(document.form`$type`);" button_title=$lng.lbl_update style="btn-green push-5-r push-20"}
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form(document.form`$type`, 'delete');" button_title=$lng.lbl_delete_selected style="btn-danger push-5-r push-20"}
    {/if}
    {include file='admin/buttons/button.tpl' href="index.php?target=memberships&mode=add&type=`$type`" button_title=$lng.lbl_add_new style="btn-green push-5-r push-20"}</div>

</form>
{/capture} 
{include file='admin/wrappers/block.tpl' content=$smarty.capture.section title=$title}

