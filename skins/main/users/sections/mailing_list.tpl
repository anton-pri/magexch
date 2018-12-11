<input type="hidden" name="update_fields[mailing_list][0]" value="0" />
<table class="header" width="100%">
<tr>
    <th width="1%" colspan="2" style="text-align: center;">{$lng.lbl_subscribed}</th>
    <th rowspan="2"  style="text-align: center;">{$lng.lbl_mailing_list}</th>
</tr>
<tr>
    <th width="1%" align="center" style="white-space:nowrap;">{$lng.lbl_by_membership}</th>
    <th width="1%" align="center">{$lng.lbl_manually}</th>
</tr>

{if $newslists}
{foreach from=$newslists item=nl}
{assign var=list_id value=$nl.list_id}
<tr {cycle values=', class="cycle"'}>
    <td align="center">{if $subscribed.$list_id && $subscribed.$list_id.by_membership eq 1}{*<input title='Subscribed by membership' type="checkbox" checked disabled />*}<img src="{$ImagesDir}/admin/checked_admin.png" />{/if}</td>
    <td align="center"><input type="checkbox" name="update_fields[mailing_list][{$list_id}]"{if $subscribed.$list_id && $subscribed.$list_id.direct eq 1} checked{/if} value="1" /></td>
    <td>{$nl.name}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="2" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
{include file='main/users/sections/custom.tpl'}
