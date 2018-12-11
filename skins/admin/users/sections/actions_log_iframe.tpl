{include file='common/navigation.tpl'}

<table width="100%" class="header">
<tr>
    <th width="10%">{$lng.lbl_date}</th>
    <th>{$lng.lbl_action}</th>
{*
    <th>{$lng.lbl_params}</th>
*}
</tr>
{if $actions}
{foreach from=$actions item=action}
<tr{cycle values=', class="cycle"'}>
    <td nowrap>{$action.date|date_format:$config.Appearance.datetime_format}</td>
    <td>{$action.title}</td>
{*
    <td>{$action.descr}</td>
*}
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>

{include file='common/navigation.tpl'}
