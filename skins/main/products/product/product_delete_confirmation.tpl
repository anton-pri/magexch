{include file='common/page_title.tpl' title=$lng.lbl_delete_products}

<br />

{capture name=section}

{if $section eq ""}
<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_return_to_search_results href="index.php?target=search&mode=search&page=`$navpage`"}</div>
<br />
{/if}

<form action="index.php?target=process_product" method="post" name="processform">

<input type="hidden" name="section" value="{$section}" />
<input type="hidden" name="cat" value="{$cat}" />

<input type="hidden" name="action" value="" />
<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="navpage" value="{$navpage}" />

<span class="Text">{$lng.lbl_product_delete_confirmation_header}:</span>

<br /><br />

<ul>
{section name=prod loop=$products}
<li><span class="ProductPriceSmall">{$products[prod].productcode} {$products[prod].product} - {include file='common/currency.tpl' value=$products[prod].price}</span>
<dl>
<dd>{$products[prod].category}</dd>
<dd>{$lng.lbl_warehouse}: {$products[prod].warehouse}</dd>
</dl>
</li>
{/section}
</ul>

<br />

{$lng.txt_operation_not_reverted_warning}

{if $search_return ne ''}
{assign var="url_to" value=$search_return} 
{elseif $section eq "category_products"}
{assign var="url_to" value="category_products.php?cat=`$cat`&page=`$navpage`"}
{else}
{assign var="url_to" value="search.php?mode=search&page=`$navpage`"}
{/if}

<br /><br />
<table cellspacing="0" cellpadding="2">
<tr>
	<td>{$lng.txt_are_you_sure_to_proceed}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/yes.tpl" href="javascript: cw_submit_form(document.processform, 'delete')"}</td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/no.tpl" href=$url_to}</td>
</tr>
</table>

</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_confirmation content=$smarty.capture.section extra='width="100%"'}
