<form method="post" action="{$current_location}/index.php?target=search" name="products_search_form">
<input type="hidden" name="simple_search" value="Y" />
<input type="hidden" name="action" value="search" />
<input type="hidden" name="posted_data[by_title]" value="Y" />
<input type="hidden" name="posted_data[by_shortdescr]" value="Y" />
<input type="hidden" name="posted_data[by_fulldescr]" value="Y" />
<input type="hidden" name="posted_data[by_manufacturer]" value="Y" />
<input type="hidden" name="posted_data[by_ean]" value="Y" />
<input type="hidden" name="posted_data[by_sku]" value="Y" />
<input type="hidden" name="posted_data[including]" value="all" />
<input type="text" name="posted_data[substring]" value="{$search_prefilled.substring|escape}" />
{include file='buttons/go_search.tpl' href="javascript: cw_submit_form('products_search_form');" button_title=$lng.lbl_go style='go'}
{*<span class="right search_lang"><a href="index.php?target=search">{$lng.lbl_advanced_search}&nbsp;&raquo;</a></span>*}

</form>
