{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
{capture name=section}

<form action="index.php?target={$current_target}" method="post" name="section_arrivals_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="js_tab" value="{$included_tab}">

<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th align="left" style="width:10px;"><input type='checkbox' class='select_all' class_to_select='arrivals_item' /></th>
    <th style="width:500px;">{$lng.lbl_product}</th>
    <th class="text-center">{$lng.lbl_active}</th>
    <th class="text-center">{$lng.lbl_pos}</th>
    <th class="text-center">{$lng.lbl_side_box}</th>
</tr>
</thead>
{if $arrivals}
{foreach from=$arrivals item=sec}
<tr {cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="data[{$sec.id}][del]" value="Y" class="arrivals_item" /></td>
    <td style="width: 200px;">
        <input type="hidden" name="data[{$sec.id}][product_id]" value="{$sec.product_id}" />
        <a href="index.php?target=products&mode=details&product_id={$sec.product_id}">{$sec.product}</a>
        {if $sec.from_time}({$sec.from_time|date_format:$config.Appearance.date_format} - {$sec.to_time|date_format:$config.Appearance.date_format}, {$lng.lbl_min_amount}: {$sec.min_amount}){/if}
    </td>
    <td align="center">
        <input type="hidden" name="data[{$sec.id}][active]" value="0" />
        <input type="checkbox" name="data[{$sec.id}][active]" value="1" {if $sec.active}checked{/if}/>
    </td>
    <td align="center"><input type="text" class="form-control" name="data[{$sec.id}][pos]" value="{$sec.pos}" size="3" /></td>
    <td align="center">
        <input type="hidden" name="data[{$sec.id}][side_box]" value="0" />
        <input type="checkbox" name="data[{$sec.id}][side_box]" value="1" {if $sec.side_box}checked{/if}/>
    </td>
</tr>
{/foreach}
<tr><td colspan="6">{include file='admin/buttons/button.tpl' href="javascript:document.section_arrivals_form.submit();" button_title=$lng.lbl_update_delete style="btn-danger"}</td></tr>
{else}
<tr>
    <td colspan="6" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
{if $accl.__1207}
<thead>
<tr><th colspan="6">{$lng.lbl_add_new}</th></tr>
</thead>
<tr>
    <td colspan="2">
		{product_selector name_for_id='newproduct_id' name_for_name='newproduct' prefix_id='section_arrivals_form' hide_id_field=1 form='section_arrivals_form'}
    </td>
    <td align="center"><input type="checkbox" name="data[0][active]" value="1" /></td>
    <td align="center"><input type="text" class="form-control" name="data[0][pos]" value="" size="3" /></td>
    <td align="center"><input type="checkbox" name="data[0][side_box]" value="1" /></td>
</tr>
{/if}
</table>

<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.section_arrivals_form, 'add');" button_title=$lng.lbl_add acl='__1207' style="btn-green push-20"}</div>


</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section}
