<h1>{$lng.lbl_product_links}</h1>

{capture name=section}

{if $product}
  <h2>{$product.product}</h2>
{/if}

<br />

<p>{$lng.txt_product_links_top_text}</p>

<br />
<br />

<h3>{$lng.lbl_product_link_thumbnail}</h3>

<br />
<br />

{capture name="product_thumbnail"}
	{include file='common/thumbnail.tpl' image=$product.image_det}
{/capture}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td valign="top" width="20%">
	{include file='common/thumbnail.tpl' image=$product.image_det}
  </td>
  <td>&nbsp;</td>
  <td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">{$smarty.capture.product_thumbnail|escape}</textarea>
  </td>
</tr>

</table>

<br />
<br />

{*** Simple HTML link to add 1 product to cart ***}

<h3>{$lng.lbl_add_1_product_link}</h3>

<br /><br />
{capture name="add_to_cart"}
	{include file='buttons/button.tpl' button_title=$lng.lbl_add_to_cart style='btn' href="`$current_location`/index.php?target=cart&amp;mode=add&amp;action=add&amp;product_id=`$product.product_id`&amp;amount=1"}
{/capture}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">
	{$smarty.capture.add_to_cart|escape}
</textarea>
  </td>
</tr>

</table>

{/capture}
{include file="common/section.tpl" title=$product.producttitle content=$smarty.capture.section extra='width="100%"'}

