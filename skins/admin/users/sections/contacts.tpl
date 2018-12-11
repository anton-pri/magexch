<table class="header" width="100%">
<tr>
    <th width="1%">&nbsp;</th>
{foreach from=$profile_fields.contact_list item=field}
    <th>{$field.title}</th>
{/foreach}
    <th>&nbsp;</th>
</tr>
{if $contact_lists}
{foreach from=$contact_lists item=contact_list}
{assign var='custom_fields' value=$contact_list.custom_fields}
<tr valign="top">
    <td align="center"><input type="checkbox" name="del[{$contact_list.contact_list_id}]" value="1"></td>
{foreach from=$profile_fields.contact_list item=field}
{assign var='fld' value=$field.field}
{assign var='fld_id' value=$field.field_id}
    <td>
    {if $field.type eq 'D'}{$contact_list.$fld}
    {else}{$custom_fields.$fld_id}
    {/if}
    </td>
{/foreach}
    <td><a href="index.php?target={$current_target}{if $current_area eq 'A'}&user={$user}{/if}&mode={$mode}&contact_list_id={$contact_list.contact_list_id}">{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
