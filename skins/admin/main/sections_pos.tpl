{capture name=section}
{capture name=block}

<form action="index.php?target=sections_pos&mode=update" method="post" name="sections_pos">
<input type="hidden" name="action" value="update" />

<table width="100%" class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th>{$lng.lbl_section}</th>
    <th>{$lng.lbl_status}{*$lng.lbl_location*}</th>
    <th>{$lng.lbl_position}</th>
</tr>
</thead>
{foreach from=$sections key=sec item=item}
<tr {cycle values=" class='cycle',"}>
    <td><b>{$item.title}</b>
     {include file="admin/main/section_title.tpl" section_name=$item.section}
    </td>
    <td width="120">
        <select class="form-control" name="positions[{$sec}][location]">
        <option value="L" {if $item.location eq 'L'}selected{/if}>{$lng.lbl_enable}</option>
        <option value="R" {if $item.location eq 'R'}selected{/if}>{$lng.lbl_disable}</option>
        </select>
    </td>
    <td align="center"  width="60"><input type="text" class="form-control" size="4" name="positions[{$sec}][orderby]" value="{$item.orderby}"></td>
</tr>
{/foreach}
</table>

<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('sections_pos');" style="btn-green push-20"}</div>
</form>
{/capture}
{include file='admin/wrappers/block.tpl' content=$smarty.capture.block}
{/capture}
{include file='admin/wrappers/section.tpl' title=$lng.lbl_sections_pos content=$smarty.capture.section}

