{*include file='common/page_title.tpl' title=$lng.lbl_speed_bar*}
{capture name=section}

{if $mode eq 'modify'}
{capture name=block2}

<div class="form-horizontal">
<form action="index.php?target=speed_bar" method="post" name="speedbarform">
<input type="hidden" name="action" value="update_one" />
<input type="hidden" name="item_id" value="{$bar.item_id}" />

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_link_title}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="update_speed_bar[title]" value="{$bar.title|escape}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_url}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="update_speed_bar[link]" value="{$bar.link|escape}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    	{$lng.lbl_active} 
        <input type="hidden" name="update_speed_bar[active]" value="0" />
    	<input type="checkbox" name="update_speed_bar[active]" value="1"{if $bar.active} checked="checked"{/if} />
    </label>

</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_pos}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" size="3" maxlength="5" name="update_speed_bar[orderby]" value="{$bar.orderby}" />
	</div>
</div>

{include file='admin/attributes/object_modify.tpl'}

<div class="buttons"><input type="submit" value="{$lng.lbl_update}" class="btn btn-green push-20" /></div>

</form>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}
{else}

{capture name=block}

<div class="box">
{include file='main/select/edit_lng.tpl' script='index.php?target=speed_bar'}
<form action="index.php?target=speed_bar" method="post" name="speedbarform">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th><input type='checkbox' class='select_all' class_to_select='speedbar_item' /></th>
	<th>{$lng.lbl_link_title}</th>
	<th>{$lng.lbl_url}</th>
	<th class="text-center">{$lng.lbl_active}</th>
    <th class="text-center">{$lng.lbl_pos}</th>
</tr>
</thead>
{if $speed_bar}
{foreach from=$speed_bar item=sb}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="update_speed_bar[{$sb.item_id}][del]" value="1" class="speedbar_item" /></td>
	<td><a href="index.php?target=speed_bar&speed_id={$sb.item_id}">{$sb.title}</a></td>
	<td><a href="index.php?target=speed_bar&speed_id={$sb.item_id}">{$sb.link|escape}</a></td>
	<td align="center">
        <input type="hidden" name="update_speed_bar[{$sb.item_id}][active]" value="0" />
        <input type="checkbox" name="update_speed_bar[{$sb.item_id}][active]" value="1"{if $sb.active} checked="checked"{/if} />
    </td>
    <td align="center"><input type="text" class="form-control" size="3" maxlength="5" name="update_speed_bar[{$sb.item_id}][orderby]" value="{$sb.orderby}" /></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="6" align="center">{$lng.lbl_no_links_defined}</td>
</tr>
{/if}
</table>

<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('speedbarform');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="index.php?target=speed_bar&speed_id=" style="btn-green push-20 push-5-r"}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
</div>
{/if}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_speed_bar}
