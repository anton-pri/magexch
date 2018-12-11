{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="section_hot_deals_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="js_tab" value="{$included_tab}">

<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th align="center" style="width:10px;"><input type='checkbox' class='select_all' class_to_select='hot_deals_item' /></th>
    <th style="width:500px;">{$lng.lbl_product}</th>
    <th class="text-center">{$lng.lbl_active}</th>
    <th class="text-center">{$lng.lbl_pos}</th>
    <th class="text-center">{$lng.lbl_week_deal}</th>
    <th class="text-center">{$lng.lbl_home_page}</th>
    <th class="text-center">{$lng.lbl_hot_deal}</th>
</tr>
</thead>
{if $hot_deals}
{foreach from=$hot_deals item=sec}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="data[{$sec.id}][del]" value="Y" class="hot_deals_item" /></td>
    <td>
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
        <input type="hidden" name="data[{$sec.id}][week_deal]" value="0" />
        <input type="checkbox" name="data[{$sec.id}][week_deal]" value="1" {if $sec.week_deal}checked{/if}/>
    </td>
    <td align="center">
        <input type="hidden" name="data[{$sec.id}][home_page]" value="0" />
        <input type="checkbox" name="data[{$sec.id}][home_page]" value="1" {if $sec.home_page}checked{/if}/>
    </td>
    <td align="center">
        <input type="checkbox" name="hot_deal" value="{$sec.id}" class='hot_deal_flag' {if $sec.hot_deal}checked="checked"{/if} onclick='javascript: $("input.hot_deal_flag").removeAttr("checked"); if ($(this).attr("checked")!="") $(this).attr("checked","checked"); else $(this).removeAttr("checked");' />
    </td>
</tr>
{/foreach}
<tr><td colspan="7">{include file='admin/buttons/button.tpl' href="javascript:document.section_hot_deals_form.submit();" button_title=$lng.lbl_update_delete style="btn-danger"}</td></tr>
{else}
<tr>
    <td colspan="7" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
{if $accl.__1207}
<thead>
<tr><th colspan="7">{$lng.lbl_add_new}</th></tr>
</thead>
<tr>

    <td colspan="2">
		{product_selector name_for_id='newproduct_id' name_for_name='newproduct' prefix_id='section_hot_deals_form' hide_id_field=1 form='section_hot_deals_form'}
    </td>
    <td align="center">
        <input type="checkbox" name="data[0][active]" value="1" />
    </td>
    <td align="center"><input class="form-control" type="text" name="data[0][pos]" value="" size="3" /></td>
    <td align="center">
        <input type="checkbox" name="data[0][week_deal]" value="1" />
    </td>
    <td align="center">
        <input type="checkbox" name="data[0][home_page]" value="1" />
    </td>
    <td align="center">
        <input type="checkbox" name="data[0][hot_deal]" value="1" />
    </td>
</tr>
{/if}
</table>

<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.section_hot_deals_form, 'add');" button_title=$lng.lbl_add acl='__1207' style="btn-green push-20"}</div>


</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_hot_deals}

