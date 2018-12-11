{if $config.Appearance.categories_in_products eq '1'}
<div class="input_field_0 search_in_category">
    <label>{$lng.lbl_search_in_category}</label>
    {include file='main/select/category.tpl' name="`$prefix`[category_id]" value=$search_prefilled.category_id}
</div>
<div class="input_field_1 search_as">
    <label>&nbsp;</label>
       <div class="labels"><label style="width: 30px;">{$lng.lbl_as}</label>
    <label><input type="checkbox" id="category_main" name="{$prefix}[category_main]"{if $search_prefilled eq '' or $search_prefilled.category_main} checked="checked"{/if} />
    {$lng.lbl_main_category}&nbsp;</label>
    <label><input type="checkbox" id="category_extra" name="{$prefix}[category_extra]"{if $search_prefilled.category_extra} checked="checked"{/if} />
    {$lng.lbl_additional_category}</label></div>
</div>
<div class="input_field_0 search_in_subcategories">
    <label>{$lng.lbl_search_in_subcategories}</label>
    <input type="checkbox" id="search_in_subcategories" name="{$prefix}[search_in_subcategories]"{if $search_prefilled eq '' or $search_prefilled.search_in_subcategories} checked="checked"{/if} />
</div>
{/if}

<div class="input_field_0 search_by_price">
    <label>{$lng.lbl_price} ({$config.General.currency_symbol})</label>
    <input type="text" size="10" maxlength="15" name="{$prefix}[price_min]" value="{$search_prefilled.price_min|formatprice}" /> -
    <input type="text" size="10" maxlength="15" name="{$prefix}[price_max]" value="{$search_prefilled.price_max|formatprice}" />
</div>

<div class="input_field_0 search_by_weight">
    <label>{$lng.lbl_weight} ({$config.General.weight_symbol})</label>
    <input type="text" size="10" maxlength="10" name="{$prefix}[weight_min]" value="{$search_prefilled.weight_min|formatprice}" /> -
    <input type="text" size="10" maxlength="10" name="{$prefix}[weight_max]" value="{$search_prefilled.weight_max|formatprice}" />
</div>

{include file="customer/products/search_form_adv_by_attributes_more.tpl"}
