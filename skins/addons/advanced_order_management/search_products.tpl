<form action="index.php?target={$current_target}&doc_id={$doc_id}&mode=edit" method="post" name="search_products_form">
{if $is_old_products}
<input type="hidden" name="is_old_products" value="1" />
{/if}
<table class="header" width="100%">
<tr valign="top">
    <td nowrap width="500">
<div class="input_field_0">
    <label>{$lng.lbl_search_by_manufacturer}</label>
    {include file='addons/manufacturers/select/manufacturer.tpl' name='posted_data[attribute_names][manufacturer_id][]' value=$search_prefilled.attribute_names.manufacturer_id multiple=5}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_search_by_description}</label>
    #<input type="text" name="posted_data[product_id]" value="" size="6">&nbsp;
    <input type="text" name="posted_data[substring]" value="" onkeypress="javascript: return cw_search_by_submit(event, '{$doc_id}');">
    {$lng.lbl_exact_search}
    <input type="checkbox" name="posted_data[substring_exact]" value="1" checked>
</div>
    </td>
	{if $config.Appearance.categories_in_products eq '1'}
    <td>
    {include file='main/select/categories_short.tpl' name='posted_data[categories][]'}
    </td>
	{/if}
</tr>
</table>
</form>

{include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_doc_search_products('`$doc_id`', 'search_products_form');"}

<div id="search_results"></div>
