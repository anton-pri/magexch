{capture assign="modify_review_tag"}
href="javascript: void(0);" onclick="show_review_management_dialog({$review_item.review_id},{$review_item.product_id});" title="{$lng.lbl_modify_review|escape}"
{/capture}
<td width="5"><input type="checkbox" name="checked_review[{$review_item.review_id}]" class="checked_review_item"/></td>
<td><a {$modify_review_tag}>{$review_item.date}</a></td>
<td>{if $review_item.sku}<a {$modify_review_tag}>{$review_item.sku}</a>{elseif $review_item.product_id eq 0}{$lng.lbl_global_site_review|default:'global site review'}{/if}</td>
<td><a {$modify_review_tag}>{$review_item.customer}{if $review_item.customer ne $review_item.name && $review_item.name ne ''}<br/>{$review_item.name}{/if}</a></td>
<td><a {$modify_review_tag}>{include file='addons/estore_products_review/product_rating.tpl' rating=$review_item.vote_value}</a></td>
<td><a {$modify_review_tag}>{if $review_item.main_title ne ''}{$review_item.main_title}<br />{/if}{$review_item.message|escape}</a></td>
<td align="center">{$review_item.status}</td>
<td align="center">{$review_item.flag}</td>
