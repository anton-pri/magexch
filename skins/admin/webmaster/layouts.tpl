<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_layout_name}</th>
    <th>{$lng.lbl_layout_descrition}</th>
    <th width="20">&nbsp;</th>
</tr>
{foreach from=$layouts item=layout}
<tr>
    <td>{$layout.layout_id}</td>
    <td>{$layout.descr}</td>
    <td><a href="index.php?target={$current_target}&layout_id={$layout.layout_id}" target=_blank>{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
</table>
