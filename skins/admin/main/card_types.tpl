{*include file='common/page_title.tpl' title=$lng.lbl_edit_cc_types*}

{capture name=section}
{capture name=block}

{$lng.txt_edit_cc_types_top_text|substitute:"path":$catalogs.admin}

{assign var="cols_count" value=5}

<form method="post" action="index.php?target=card_types" name="ccardsform">
<input type="hidden" name="action" value="update" />

<table class="table table-striped" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='cart_type_item' /></th>
	<th width="20%">{$lng.lbl_card_code}</th>
	<th width="60%">{$lng.lbl_card_type}</th>
    <th width="10%" class="text-center">{$lng.lbl_active}</th>
	<th width="20%" class="text-center">{$lng.lbl_cc_cvv2}*</th>
</tr>
</thead>

{if $card_types}
{foreach from=$card_types item="card" key="id"}

<tr>
	<td>
	<input type="checkbox" name="posted_data[{$id}][to_delete]" class="cart_type_item" />
	<input type="hidden" name="posted_data[{$id}][code]" value="{$card.code}" />
	<input type="hidden" name="posted_data[{$id}][old_name]" value="{$card.type}" />
	</td>
	<td> {$card.code} </td>
	<td><input type="text" class="form-control" size="50" name="posted_data[{$id}][new_name]" value="{$card.type}" /></td>
        <td align="center"><input type="checkbox" name="posted_data[{$id}][new_active]"{if $card.active} checked="checked"{/if} /></td>
	<td align="center"><input type="checkbox" name="posted_data[{$id}][new_cvv2]"{if $card.cvv2} checked="checked"{/if} /></td>
</tr>

{/foreach}

<tr>
	<td colspan="{$cols_count}" class="SubmitBox">
	<input class="btn btn-danger push-5-r" type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: cw_submit_form(document.ccardsform, "delete");' />
	<input class="btn btn-green push-5-r" type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	</td>
</tr>

{else}

<tr>
<td colspan="{$cols_count}" align="center">{$lng.txt_no_cc_types}</td>
</tr>

{/if}

<tr>
<td colspan="{$cols_count}"><br />{include file="common/subheader.tpl" title=$lng.lbl_add_cc_type}</td>
</tr>

<tr class="TableHead">
<th>&nbsp;</th>
<th>{$lng.lbl_card_code}</th>
<th>{$lng.lbl_card_type}</th>
<th class="text-center">{$lng.lbl_active}</th>
<th class="text-center">{$lng.lbl_cc_cvv2}*</th>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input class="form-control" type="text" size="10" name="code" /></td>
	<td><input class="form-control" type="text" size="50" name="new_name" /></td>
        <td align="center"><input type="checkbox" name="new_active" /></td>
	<td align="center"><input type="checkbox" name="new_cvv2" /></td>
</tr>

<tr>
	<td colspan="{$cols_count}" class="SubmitBox">
		<input class="btn btn-green" type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'add');"/>
	</td>
</tr>

</table>

</form>

<p>{$lng.txt_edit_cc_types_note}</p>


{/capture} 
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture} 
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_edit_cc_types}
