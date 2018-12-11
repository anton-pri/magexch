{*include file='common/page_title.tpl' title=$lng.lbl_payment_methods*}

{capture name=section}

{capture name=block}
<p>{$lng.txt_payment_methods_top_text}</p>

{include file='main/select/edit_lng.tpl' script='index.php?target=payments&mode=methods'}
<form action="index.php?target=payments" method="post" name="payment_methods_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update" />
<div class="box">

<table class="table table-striped" width="100%">
<thead>
<tr>
    <th width="1%"><input type='checkbox' class='select_all' class_to_select='payment_methods_item' /></th>
    <th width="10%">{$lng.lbl_payment_code}</th>
    <th width="69%">{$lng.lbl_title}</th>
    <th width="5%">{$lng.lbl_active}</th>
	<th width="5%">{$lng.lbl_pos}</th>
    <th width="10%">&nbsp;</th>
</tr>
</thead>
{foreach from=$payment_methods item=method}
<tr{cycle values=", class='cycle'"}>
    <td><input type="checkbox" name="posted_data[{$method.payment_id}][del]" value="1" class="payment_methods_item" /></td>
    <td><input type="text" class="form-control" name="posted_data[{$method.payment_id}][payment_code]" value="{$method.payment_code|escape}" /></td>
	<td><a href="index.php?target=payments&mode=methods&payment_id={$method.payment_id}">{$method.title}</a></td>
    <td class="td_center">{include file='admin/select/yes_no.tpl' name="posted_data[`$method.payment_id`][active]" value=$method.active}</td>
	<td class="td_center"><input type="text" class="form-control" size="5" maxlength="5" name="posted_data[{$method.payment_id}][orderby]" value="{$method.orderby}" /></td>
    <td align="center"><a class="btn btn-default " href="index.php?target=payments&mode=methods&payment_id={$method.payment_id}">{$lng.lbl_modify}</a></td>
</tr>
{foreachelse}
<tr>
    <td colspan="10" align="center">{$lng.lbl_not_found}</td>
</tr>
{/foreach}
</table>
</div>
{if $payment_methods}
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_delete href="javascript: cw_submit_form('payment_methods_form')" acl='__2503'  style='btn-danger push-20 push-5-r'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new  href="index.php?target=payments&mode=methods&payment_id=" acl='__2503'  style='btn-green push-20 push-5-r'}
</div>
{/if}
<div class="clear"></div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_payment_methods}

