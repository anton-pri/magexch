{capture name=adv_search}
<form action="index.php?target=search" name="product_search_by_price_form">
<div class="input_field_0">
    <label>{$lng.lbl_product_title}</label>
    <input type="text" name="substring" size="30" value="{$smarty.get.substring|escape:"html"}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_category}</label>
    {include file='main/select/category.tpl' name='in_category' value=$smarty.get.in_category}
<div>
<div class="input_field_0">
    <label>{$lng.lbl_price}, {$config.General.currency_symbol}</label>
    <input type="text" name="price_search_1" size="6" value="{$smarty.get.price_search_1|escape}" /> - 
    <input type="text" name="price_search_2" size="6" value="{$smarty.get.price_search_2|escape}" />
</div>


{include file='buttons/search.tpl' style='btn' href="javascript: cw_submit_form('product_search_by_price_form')"}
</form>
{/capture}
{include file='common/section.tpl' title=$lng.lbl_advanced_search content=$smarty.capture.adv_search}
