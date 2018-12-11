{include file='common/navigation.tpl'}

<form action="index.php?target={$current_target}&user={$user}" method="post" name="docs_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update" />

<table class="header" width="100%">
<tr>
{if $docs_type eq 'O'}
    <th width="1%">&nbsp;</th>
{/if}
    <th width="10%">{$lng.lbl_doc_id}</th>
    <th>{$lng.lbl_date}</th>
    <th>{$lng.lbl_total}</th>
</tr>
{if $orders}
{foreach from=$orders item=order}
<tr{cycle values=', class="cycle"'}>
{if $docs_type eq 'O'}
    <td><input type="checkbox" name="docs[{$order.doc_id}]" value="1" ></td>
{/if}
    <td>{$order.display_id}</td>
    <td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
    <td>{include file='common/currency.tpl' value=$order.total}</td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
{if $docs_type eq 'O' && $orders}
{include file='buttons/button.tpl' button_title=$lng.lbl_generate_group href="javascript:cw_submit_form('docs_form', 'generate_group');"}
{/if}
</form>

{include file='common/navigation.tpl'}
