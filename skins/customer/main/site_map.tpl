{capture name=section}
{foreach from=$site_categories item=category}
<a href="{pages_url var="index" cat=$category.category_id}">{$category.category}</a><br />
{/foreach}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_categories content=$smarty.capture.section}

{capture name=section}
{foreach from=$site_products item=product}
<a href="{pages_url var="product" product_id=$product.product_id}" class="VertMenuItems">{$product.product}</font></a>
{/foreach}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_products content=$smarty.capture.section}

{capture name=section}
{foreach from=$site_manufacturers item=manufacturer}
<a href="{pages_url var="manufacturers" manufacturer_id=$manufacturer.manufacturer_id}">{$manufacturer.manufacturer}</a><br />
{/foreach}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_manufacturers content=$smarty.capture.section}

