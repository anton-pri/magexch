
<table cellpadding="3" cellspacing="1" width="100%">

<tr>
<td>

<form action="index.php?target=configuration" method="post">
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="action" value="update_status" />

<table cellpadding="3" cellspacing="1" width="100%">


<tr class="TableHead">
	<td rowspan="2" width="30%" nowrap="nowrap">{$lng.lbl_field_name}</td>
{foreach from=$usertypes_array item=to_disable key=utype}
	<td align="center">
	{if $utype eq "B"}{$lng.lbl_salesman}{elseif $utype eq "P"}{$lng.lbl_warehouse}{else}{$lng.lbl_customer}{/if}
	</td>
{/foreach}
</tr>

{math equation="floor(80/x)" x=$usertypes_array_count assign="col_width"}

<tr class="TableHeadLevel2">
{foreach from=$usertypes_array item=to_disable key=utype}
	<td width="{$col_width}%" align="center" nowrap="nowrap">{$lng.lbl_active} / {$lng.lbl_required}</td>
{/foreach}
</tr>

{math equation="x*2+1" x=$usertypes_array_count assign="colspan"}

{foreach from=$profile_fields item=item key=field}

{if $item.field eq "department"}
{assign var="part_title" value=$lng.lbl_contact_information}
{elseif $item.field eq "custom_section"}
{assign var="part_title" value=$item.title}
{else}
{assign var="part_title" value=""}
{/if}

{if $part_title}
<tr>
    <td colspan="{$colspan}"><br />{include file="common/subheader.tpl" title=$part_title class="grey"}</td>
</tr>
{/if}

{if $item.field ne 'custom_section'}
<tr {cycle values=", class='cycle'"}
	<td>
	{$item.title}
	<input type="hidden" name="default_data[{$item.field}][flag]" value="Y" />
	</td>
{foreach from=$usertypes_array item=to_disable key=utype}
	<td align="center">
	<input type="checkbox" onclick="javascript: document.getElementById('dr_{$item.field}_{$utype}').disabled = !this.checked;" name="default_data[{$item.field}][avail][{$utype}]"{if $item.avail.$utype eq "Y"} checked="checked"{/if} />
	&nbsp;/&nbsp;
	<input type="checkbox" id="dr_{$item.field}_{$utype}" name="default_data[{$item.field}][required][{$utype}]"{if $item.required.$utype eq "Y"} checked="checked"{/if}{if $item.avail.$utype ne "Y"} disabled="disabled"{/if} />
	</td>
{/foreach}
</tr>
{/if}

{if $additional_fields ne ''}
{foreach from=$additional_fields item=v key=k}
{if $v.section eq $item.section || ($item.field eq "url" && $v.section eq 10)}
<tr {cycle values=", class='cycle'"}
	<td>{$v.title|default:$v.field}</td>
{foreach from=$usertypes_array item=to_disable key=utype}
	<td align="center">	
	<input type="checkbox" onclick="javascript: document.getElementById('ar_{$v.field_id}_{$utype}').disabled = !this.checked;" name="add_data[{$v.field_id}][avail][{$utype}]"{if $v.avail.$utype eq "Y"} checked="checked"{/if} />
	&nbsp;/&nbsp;
	<input id="ar_{$v.field_id}_{$utype}" type="checkbox" name="add_data[{$v.field_id}][required][{$utype}]"{if $v.required.$utype eq "Y"} checked="checked"{/if}{if $v.avail.$utype ne "Y"} disabled="disabled"{/if} />
	</td>
{/foreach}
</tr>
{/if}
{/foreach}
{/if}

{/foreach}

<tr>
	<td colspan="{$colspan}"><br />
	<input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " />
	</td>
</tr>

</table>
</form>
<br /><br />

<form action="index.php?target=configuration" method="post" name="fieldsform">
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="action" value="update_fields" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td colspan="4"><br />{include file="common/subheader.tpl" title=$lng.lbl_additional_fields}</td>
</tr>

<tr class="TableHead">
	<td>&nbsp;</td>
	<td nowrap="nowrap">{$lng.lbl_field_name}</td>
    <td>{$lng.lbl_section}</td>
	<td>{$lng.lbl_type}</td>
	<td nowrap="nowrap">{$lng.lbl_pos}</td>
</tr>

{if $additional_fields}
{foreach from=$additional_fields item=v}
<tr>
	<td><input type="checkbox" name="fields[{$v.field_id}]" value="Y" /></td>
	<td><input type="text" size="30" maxlength="100" name="update[{$v.field_id}][field]" value="{$v.title|default:$v.field}" /></td>
    <td>
    <select name="update[{$v.field_id}][section]">
    {foreach from=$sections item=s key=k}
        <option value="{$s.section}"{if $v.section eq $s.section} selected="selected"{/if}>{$s.name}</option>
    {/foreach}
    </select>
    </td>
	<td><select name="update[{$v.field_id}][type]">
	{foreach from=$types item=t key=k}
	<option value="{$k}"{if $v.type eq $k} selected="selected"{/if}>{$t}</option>
	{/foreach}
	</select></td>
	<td><input type="text" name="update[{$v.field_id}][orderby]" value="{$v.orderby}" size="5" /></td>
</tr>
{if $v.type eq 'S'}
<tr>
    <td>&nbsp;</td>
    <td colspan="3"><input type="text" size="60" name="update[{$v.field_id}][variants]" value="{$v.variants}" /></td>
</tr>
{/if}
{/foreach}

<tr>
	<td colspan="4"><br />
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript:document.fieldsform.mode.value='delete';document.fieldsform.submit();" />
	</td>
</tr>

{else}

<tr>
	<td colspan="4" align="center">{$lng.txt_no_additional_fields}</td>
</tr>

{/if}

<tr>
	<td colspan="4"><br />{include file="common/subheader.tpl" title=$lng.lbl_add_new_field}</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input type="text" name="newfield" size="30" maxlength="100" /></td>
	<td>
    <select name="newfield_section">
    {foreach from=$sections item=v key=k}
        <option value="{$v.section}">{$v.name}</option>
    {/foreach}
    </select>
    </td>
	<td>
	<select name="newfield_type">
	{foreach from=$types item=v key=k}
	<option value="{$k}">{$v}</option>
	{/foreach}
	</select>
	</td>
	<td><input type="text" size="5" name="newfield_orderby" /></td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td colspan="3">{$lng.lbl_variants_for_selectbox}:</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="3"><input type="text" size="60" name="newfield_variants" /></td>
</tr> 



<tr>
	<td colspan="4"><br />
	<input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" />
	</td>
</tr>

</table>
</form>

</td>
</tr>
</table>

<br /><br />

<form action="index.php?target=configuration" method="post" name="fieldsform">
<input type="hidden" name="option" value="{$option}" />
<input type="hidden" name="action" value="update_sections" />

<table cellpadding="3" cellspacing="1">

<tr>
    <td colspan="5"><br />{include file="common/subheader.tpl" title=$lng.lbl_additional_sections}</td>
</tr>

<tr class="TableHead">
    <td width="1%">&nbsp;</td>
    <td>{$lng.lbl_section}</td>
    <td nowrap="nowrap">{$lng.lbl_pos}</td>
</tr>

{if $sections}
{foreach from=$sections item=v}
<tr>
    <td><input type="checkbox" name="fields[{$v.section}]" value="Y" {if $v.is_default}disabled{/if}/></td>
    <td><input type="text" size="60" maxlength="100" name="update[{$v.section}][name]" value="{$v.name}" /></td>
    <td><input type="text" name="update[{$v.section}][orderby]" value="{$v.orderby}" size="5" /></td>
</tr>
{/foreach}

<tr>
    <td colspan="5"><br />
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'delete_sections');" />
    </td>
</tr>

{/if}

<tr>
    <td colspan="5"><br />{include file="common/subheader.tpl" title=$lng.lbl_add_new_section}</td>
</tr>

<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="newsection" size="60" maxlength="100" /></td>
    <td><input type="text" size="5" name="newsection_orderby" /></td>
</tr>

<tr>
    <td colspan="5" class="SubmitBox"><input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
