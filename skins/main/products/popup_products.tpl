<script type="text/javascript">
<!--
var err_choose_product = "{$lng.err_choose_product|strip_tags|replace:"\n":" "|replace:"\r":" "|replace:'"':'\"'}";
var err_choose_category = "{$lng.err_choose_category|strip_tags|replace:"\n":" "|replace:"\r":" "|replace:'"':'\"'}";
var product_ids = new Array();
var product_bookmark_ids = new Array();
/*
var id_obj = window.opener.document.{$field_product_id|stripslashes} ? window.opener.document.{$field_product_id|stripslashes} : window.opener.document.getElementById('{$field_product_id|id}');
var name_obj = window.opener.document.{$field_product|stripslashes} ? window.opener.document.{$field_product|stripslashes} : window.opener.document.getElementById('{$field_product}');
var amount_obj = {if $field_amount}window.opener.document.{$field_amount|stripslashes} ? window.opener.document.{$field_amount|stripslashes} : window.opener.document.getElementById('{$field_amount|id}'){else}''{/if};
*/
var id_obj = window.parent.document.{$field_product_id|stripslashes} ? window.parent.document.{$field_product_id|stripslashes} : window.parent.document.getElementById('{$field_product_id|id}');
var name_obj = window.parent.document.{$field_product|stripslashes} ? window.parent.document.{$field_product|stripslashes} : window.parent.document.getElementById('{$field_product}');
var amount_obj = {if $field_amount}window.parent.document.{$field_amount|stripslashes} ? window.parent.document.{$field_amount|stripslashes} : window.parent.document.getElementById('{$field_amount|id}'){else}''{/if};

{literal}
function clearProduct() {
    if (id_obj)
        id_obj.value = '';
    if (name_obj)
        name_obj.value = '';
}

function setProduct(product_id, product, amount) {
	if (id_obj)
		id_obj.value = id_obj.value+' '+product_id;
	if (name_obj)
		name_obj.value = name_obj.value+' '+product;
    if (amount_obj && isset(amount))
        amount_obj.value = amount_obj.value+' '+amount;

}

function setProductInfo() {
    clearProduct();
    flag = false;
    for(i in product_ids) 
        if (document.getElementById('products_'+i).checked) {
            setProduct(i, product_ids[i], 1);
            flag = true;
        }

    if (flag) {
        window.parent.hm('products_dialog');
	}
	else {
		alert(err_choose_product);
	}
		
	return false;
		
}

function setProductBookmarks() {
    clearProduct();
    for(i in product_bookmark_ids) {
        el = document.getElementById('bookmark_amount_'+i);
        amount = 1;
        if (el) amount = el.value;
        setProduct(product_bookmark_ids[i]['product_id'], product_bookmark_ids[i]['product'], amount);
    }

    window.parent.hm('products_dialog');

}
{/literal}
-->
</script>

{if $bookmarks}
<form name="bookmarks_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="update_bookmarks" />
<input type="hidden" name="field_product" value="{$field_product}" />
<input type="hidden" name="field_product_id" value="{$field_product_id}" />
<input type="hidden" name="field_amount" value="{$field_amount}" />

<table class="table table-striped" width="100%">
<thead>
<tr>
    <th width="1%">&nbsp;</th>
    <th width="10%">{$lng.lbl_sku}</th>
    <th>{$lng.lbl_product}</th>
    {if $field_amount}
    <th width="5%">{$lng.lbl_amount}</th>
    {/if}
</tr>
</thead>
{foreach from=$bookmarks item=bookmark name="bookmark"}
<tr>
    <td><input type="checkbox" name="del_bookmark[{$smarty.foreach.bookmark.index}]" value="1"/></td>
    <td nowrap>{$bookmark.productcode}</td>
    <td>{$bookmark.product}</td>
    {if $field_amount}
    <td><input type="text" id="bookmark_amount_{$smarty.foreach.bookmark.index}" value="1" size="5"/></td>
    {/if}
</tr>
<script language="javascript">
    product_bookmark_ids['{$smarty.foreach.bookmark.index}'] = new Array();
    product_bookmark_ids['{$smarty.foreach.bookmark.index}']['product_id'] = '{$bookmark.product_id}';
    product_bookmark_ids['{$smarty.foreach.bookmark.index}']['product'] = "{$bookmark.product|replace:"\n":" "|replace:"\r":" "|replace:'"':'\"'}";
</script>
{/foreach}
</table>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript: setProductBookmarks();" style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript: cw_submit_form(document.bookmarks_form);" style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_all href="javascript: cw_submit_form(document.bookmarks_form, 'delete_all_bookmarks');" style="btn-danger push-5-r push-20"}
</form>
{/if}


{jstabs}
default_tab={$js_tab|default:"basic_search"}
default_template=admin/products/search_form.tpl

[submit]
title="{$lng.lbl_search}"
style="btn-green push-20 push-5-r"
href="javascript: cw_submit_form(document.search_form, 'search');"

[reset]
title="{$lng.lbl_reset}"
style="btn-danger push-20 push-5-r"
href="javascript: cw_submit_form(document.search_form, 'reset');"

[basic_search]
title="{$lng.lbl_search_products}"
display=always

[add_search]
title="{$lng.lbl_advanced_products_search}"

[prices]
title={$lng.lbl_prices}

[additional_options]
title="{$lng.lbl_additional_options}"

{/jstabs}

<form name="search_form" action="index.php?target={$current_target}" method="post" class='tab_general_content'>
<input type="hidden" name="action" value="search" />
<input type="hidden" name="field_product" value="{$field_product}" />
<input type="hidden" name="field_product_id" value="{$field_product_id}" />
<input type="hidden" name="field_amount" value="{$field_amount}" />

{include file='tabs/search_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>

{if $products}
<script type="text/javascript">
{literal}
      $(".hide div.content").css({'display' : 'none'});
{/literal}

      $(".hide div.content").after($('<a class="expand_search">{$lng.lbl_new_search}</a>'));
{literal}

      $("a.expand_search").click(function () {
           $(".hide div.content").css({'display' : 'block'});
           $("a.expand_search").remove();
      });

{/literal}
</script>

{capture name=section}

<div class="row">
	<div class="col-xs-6 left-align">{include file='common/navigation_counter.tpl'}</div>

	<div class="col-xs-6">{include file='common/navigation.tpl'}</div>
</div>
<form action="index.php?target=popup_products" method="post"  name="products_form">
<input type="hidden" name="action" value="bookmark" />
<input type="hidden" name="field_product_id" value="{$field_product_id|escape:"html"}" />
<input type="hidden" name="field_product" value="{$field_product|escape:"html"}" />
<input type="hidden" name="field_amount" value="{$field_amount}" />

<table class="table table-striped" width="100%">
<thead>
<tr>
    <th width="5">&nbsp;</th>
    <th>{$lng.lbl_sku}</th>
    <th>{$lng.lbl_product}</th>
    <th>{$lng.lbl_in_stock}</th>
</tr>
</thead>
{section name=prod loop=$products}
<tr{cycle values=', class="cycle"'}>
    <td width="5">
        <input type="radio" name="finded_products" id="products_{$products[prod].product_id}" />
<script language="javascript">
product_ids['{$products[prod].product_id}'] = "{$products[prod].product|escape:json}";
</script>
    </td>
    {if $field_amount}
    {/if}
    <td>{$products[prod].productcode}</td>
    <td>{$products[prod].product}</td>
    <td nowrap>
{if $products[prod].is_variants ne 'Y'}
{$products[prod].avail|default:0}
{if $products[prod].avails}
{include file="main/visiblebox_link.tpl" mark="open_close_product`$products[prod].product_id`"}
{/if}
{/if}
    </td>
</tr>
{if $products[prod].avails}
<tr style="display:none;" id="open_close_product{$products[prod].product_id}">
    <td>&nbsp;</td>
    <td colspan="4">
    {include file='main/products/product/avails.tpl' avails_summ=$products[prod].avails simple=true}
    </td>
</tr>
{/if}
{/section}
</table>
<br />
<div id="sticky_content" class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript: setProductInfo();" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_to_bookmarks href="javascript: cw_submit_form(document.products_form);" style="btn-green push-20 push-5-r"}
</div>
</form>

{/capture}
{include file="admin/wrappers/block.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"' style="popup"}
{/if}
