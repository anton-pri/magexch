{ include file='common/page_title.tpl' title=$lng.lbl_search_products }

<!-- IN THIS SECTION -->



<!-- IN THIS SECTION -->

<br />

{if $mode ne "search" or $products eq ""}

{include_once_src file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var categories_in_products = "{$config.Appearance.categories_in_products}";

var searchform_def = new Array();
if (categories_in_products == '1') {
searchform_def[0] = new Array('posted_data[category_main]', true);
searchform_def[1] = new Array('posted_data[search_in_subcategories]', true);
}
searchform_def[2] = new Array('posted_data[by_title]', true);
searchform_def[3] = new Array('posted_data[by_shortdescr]', true);
searchform_def[4] = new Array('posted_data[by_fulldescr]', true);
searchform_def[5] = new Array('posted_data[by_keywords]', true);
searchform_def[6] = new Array('posted_data[price_min]', '{$zero}');
searchform_def[7] = new Array('posted_data[avail_min]', '0');
searchform_def[8] = new Array('posted_data[weight_min]', '{$zero}');
-->
</script>

{capture name=section}

<br />

<form name="searchform" action="index.php?target=search" method="post">
<input type="hidden" name="action" value="search" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
<input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring|escape}" />
&nbsp;
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
	</td>
</tr>

{if $config.search.allow_search_by_words eq 'Y'}
<tr>
<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_including}:</td>
<td width="10" height="10"></td>
<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" name="posted_data[including]" value="all"{if $search_prefilled eq "" or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} /></td>
	<td nowrap="nowrap">{$lng.lbl_all_word}&nbsp;&nbsp;</td>

	<td width="5"><input type="radio" name="posted_data[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} /></td>
	<td nowrap="nowrap">{$lng.lbl_any_word}&nbsp;&nbsp;</td>

	<td width="5"><input type="radio" name="posted_data[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} /></td>
	<td nowrap="nowrap">{$lng.lbl_exact_phrase}</td>
</tr>
</table>
</td>
</tr>
{/if}

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_shortdescr" name="posted_data[by_shortdescr]"{if $search_prefilled eq "" or $search_prefilled.by_shortdescr} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_shortdescr">{$lng.lbl_short_description}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_fulldescr" name="posted_data[by_fulldescr]"{if $search_prefilled eq "" or $search_prefilled.by_fulldescr} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_fulldescr">{$lng.lbl_det_description}</label>&nbsp;&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>

<br />

{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="box1">
<tr>
	<td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
	<td colspan="3"><br />{include file="common/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
</tr>

<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in_category}:</td>
	<td height="10"></td>
	<td height="10">
	<select name="posted_data[category_id]" style="width: 70%;">
		<option value=""></option>
{foreach from=$search_categories item=v}
		<option value="{$v.category_id}" {if $search_prefilled.category_id eq $v.category_id}selected{/if}>{$v.category_path}</option>
{/foreach}
	</select>
	</td>
</tr>

{if $config.Appearance.categories_in_products eq '1'}
<tr>
	<td colspan="2" height="10">&nbsp;</td>
	<td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5" nowrap="nowrap">{$lng.lbl_as}&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_category_main" name="posted_data[category_main]"{if $search_prefilled eq "" or $search_prefilled.category_main} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_category_main">{$lng.lbl_main_category}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_category_extra" name="posted_data[category_extra]"{if $search_prefilled.category_extra} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_category_extra">{$lng.lbl_additional_category}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td colspan="2" width="10" height="10">&nbsp;</td>
	<td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_search_in_subcategories" name="posted_data[search_in_subcategories]"{if $search_prefilled eq "" or $search_prefilled.search_in_subcategories} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_search_in_subcategories">{$lng.lbl_search_in_subcategories}</label></td>
</tr>
</table>
	</td>
</tr>
{/if}

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode}" style="width: 70%;" />
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_quantity}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="10" name="posted_data[avail_min]" value="{if $search_prefilled eq ""}0{else}{$search_prefilled.avail_min}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="10" name="posted_data[avail_max]" value="{$search_prefilled.avail_max}" /></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol}):</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="10" name="posted_data[weight_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.weight_min|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="10" name="posted_data[weight_max]" value="{$search_prefilled.weight_max|formatprice}" /></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td colspan="3" class="SubmitBox"><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>

</table>

	</td>
</tr>
</table>

</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2"><!--
visibleBox('1');
--></script>
{/if}


{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_products content=$smarty.capture.section extra='width="100%"'}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{include file="common/navigation_counter.tpl"}
{/if}

{if $mode eq "search" and $products ne ""}

<!-- SEARCH RESULTS START -->

<br /><br />

{capture name=section}

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_again href="index.php?target=search"}</div>

<form action="index.php?target=process_product" method="post" name="processproductform">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="navpage" value="{$navpage}" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td>

{include file="common/navigation.tpl"}
{include file="customer/products/products.tpl" products=$products current_area='B'}
{include file="common/navigation.tpl"}

	</td>
</tr>

</table>
</form>

<br />

{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"'}

{/if}

<br /><br />
