{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}

{foreach from=$membership_names key=memid item=mem}

{assign var=section_data value=$super_deals.$memid}


{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="section_super_deals_form_{$memid}">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="js_tab" value="{$included_tab}">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th align="center" style="width:10px;"><input type='checkbox' class='select_all' class_to_select='super_deals_item' /></th>
    <th>{$lng.lbl_product}</th>
    <th class="text-center">{$lng.lbl_active}</th>
    <th class="text-center">{$lng.lbl_pos}</th>
</tr>
</thead>
{if $section_data}
{foreach from=$section_data item=sec}
<tr{cycle values=", class='cycle'"}>
    <td  align="center"><input type="checkbox" name="data[{$sec.id}][del]" value="Y" class="super_deals_item" /></td>
    <td>
        <input type="hidden" name="data[{$sec.id}][product_id]" value="{$sec.product_id}" />
        <input type="hidden" name="data[{$sec.id}][membership_id]" value="{$memid}" />
        <a href="index.php?target=products&mode=details&product_id={$sec.product_id}">{$sec.product}</a>
        {if $sec.from_time}({$sec.from_time|date_format:$config.Appearance.date_format} - {$sec.to_time|date_format:$config.Appearance.date_format}, {$lng.lbl_min_amount}: {$sec.min_amount}){/if}
    </td>
    <td align="center">
        <input type="hidden" name="data[{$sec.id}][active]" value="0" />
        <input type="checkbox" name="data[{$sec.id}][active]" value="1" {if $sec.active}checked{/if}/>
    </td>
    <td align="center"><input type="text" class="form-control" name="data[{$sec.id}][pos]" value="{$sec.pos}" size="3" /></td>
</tr>
{/foreach}
<tr><td colspan="6">{include file='admin/buttons/button.tpl' href="javascript:document.section_super_deals_form_`$memid`.submit();" button_title=$lng.lbl_update_delete style="btn-danger"}</td></tr>
{else}
<tr>
    <td colspan="6" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
{if $accl.__1207}
<thead>
<tr><th colspan="6">{$lng.lbl_add_new}</th></tr>
</thead>
<tr valign="top">
    <td colspan="2" class="super_deals">
      <div class="col-xs-8">
        <input type="hidden" name="data[0][membership_id]" value="{$memid}" />
		{product_selector name_for_id='newproduct_id' name_for_name='newproduct' prefix_id="section_super_deals_form_`$memid`" hide_id_field=1 form="section_super_deals_form_`$memid`"}
      </div>
      <div class="col-xs-4">
        <select name="memberships[]" class="form-control" multiple size="3" >
{foreach from=$membership_names key=_memid item=_mem}
    {if $_memid ne $memid}
        <option value="{$_memid}">{$_mem}</option>
    {/if}
{/foreach}
        </select>
      </div>
    </td>
    <td align="center">
        <input type="checkbox" name="data[0][active]" value="1" />
    </td>
    <td align="center">
        <input type="text" class="form-control" name="data[0][pos]" value="" size="3" />
    </td>
</tr>
{/if}
</table>

<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('section_super_deals_form_`$memid`', 'add');" button_title=$lng.lbl_add acl='__1207' style="btn-green push-20"}
</div>

</form>
{/capture} 
{include file='admin/wrappers/block.tpl' title=$mem content=$smarty.capture.section}

{/foreach}
