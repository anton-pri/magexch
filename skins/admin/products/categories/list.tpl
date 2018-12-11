{if $cat}
{include file='common/subheader.tpl' title="`$lng.lbl_current_category`: `$current_category.category`"}
{if !$current_category.status}
<div class="ErrorMessage">{$lng.txt_category_disabled}</div>
{/if}

<div align="right" class="buttons">
{include file='buttons/button.tpl' href="index.php?target=`$current_target`&mode=edit&cat=`$current_category.category_id`" button_title=$lng.lbl_modify}
{include file='buttons/button.tpl' href="index.php?target=process_category&cat=`$current_category.category_id`&mode=delete" button_title=$lng.lbl_delete}
</div>

{include file="common/subheader.tpl" title=$lng.txt_list_of_subcategories}
{/if}

{if !$process_category_form}{assign var=process_category_form value='process_category_form'}{/if}

<form action="index.php?target=categories" method="post" name="{$process_category_form}">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="js_tab" value="{$js_tab}" />
<input type="hidden" name="action" value="apply" />
<input type="hidden" name="cat" value="{$current_category.category_id}" />

<div class="box">
<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
{if $accl.__1200}
    <th width="1%" align="center"><input type='checkbox' class='select_all' class_to_select='categories_item' /></th>
{/if}
	<th width="5%">{$lng.lbl_pos}</th>
	<th>{$lng.lbl_category_name}</th>
	<th class="text-center">{$lng.lbl_products} (<span class='subcount_enabled'>{$lng.lbl_enabled}</span><span class='subcount_all'>{$lng.lbl_all}</span>)*</th>
	<th class="text-center">{$lng.lbl_subcategories} (<span class='subcount_enabled'>{$lng.lbl_enabled}</span><span class='subcount_all'>{$lng.lbl_all}</span>)*</th>
	<th class="text-center">{$lng.lbl_enabled}</th>
</tr>
</thead>
{if $subcategories}
{foreach from=$subcategories item=c}
{assign var="catid" value=$c.category_id}

<tr{cycle values=', class="cycle"'}>
{if $accl.__1200}
	<td align="center"><input type="checkbox" name="delete_arr[{$catid}]" value="Y" class="categories_item" {if $catid eq 1}disabled="disabled"{/if} /></td>
{/if}
	<td><input type="text" size="3" name="posted_data[{$catid}][order_by]" maxlength="3" value="{$c.order_by}" class="form-control" /></td>
	<td><a href="index.php?target={$current_target}&mode=edit&cat={$catid}"><font class="{if !$c.status}ItemsListDisabled{else}ItemsList{/if}">{ $c.category|escape }</font></a></td>
	<td align="center">
		<a href="index.php?target={$current_target}&mode=products&cat={$catid}"><span class='subcount_enabled'>{$c.product_count_web|default:0} ({$c.subcounts[1].product_count|default:0})</span>
		<span class='subcount_all'>{$c.product_count|default:0} ({$c.subcounts[0].product_count|default:0})</span>
		</a>
	</td>
	<td align="center">
        <a href="index.php?target={$current_target}&cat={$catid}"><span class='subcount_enabled'>{$c.subcategory_count_web|default:0} ({$c.subcounts[1].subcategory_count|default:0})</span>
        <span class='subcount_all'>{$c.subcategory_count|default:0} ({$c.subcounts[0].subcategory_count|default:0})</span>
        </a>
    </td>
	<td align="center">
    {include file='admin/select/availability.tpl' name="posted_data[`$catid`][status]" value=$c.status}
	</td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="6" align="center">{$lng.txt_no_categories}</td>
</tr>
{/if}
</table>

</div>

{if $subcategories}
<b>*{$lng.lbl_note}:</b> {$lng.txt_categoryies_management_note}

<span class='subcount_enabled'><a href="javascript: void(0);" onclick="javascript: $('.subcount_enabled').hide();$('.subcount_all').show();">{$lng.lbl_show} {$lng.lbl_all}</a></span>
<span class='subcount_all'><a href="javascript: void(0);" onclick="javascript: $('.subcount_all').hide();$('.subcount_enabled').show();">{$lng.lbl_show} {$lng.lbl_enabled}</a></span>
<br/>
<br/>
{/if}
<div class="buttons push-20">
{if $subcategories}

{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('`$process_category_form`', 'apply');" button_title=$lng.lbl_update acl='__1200' style="push-5-r btn-green"}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('`$process_category_form`', 'list');" button_title=$lng.lbl_modify_selected acl='__1200'  style="push-5-r btn-green"}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('`$process_category_form`', 'delete');" button_title=$lng.lbl_delete_selected acl='__1200' style="push-5-r btn-danger"}
{/if}
{include file='admin/buttons/button.tpl' href="index.php?target=`$current_target`&mode=add&cat=`$cat`" button_title=$lng.lbl_add_new acl='__1200'  style="push-5-r btn-green"}
</div>
</form>
