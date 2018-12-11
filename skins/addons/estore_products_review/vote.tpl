{if $vote_reviews}
<div id="vote_reviews" class='voting'>
{section name=foo loop=6 max=5 step=-1}   
    <div class='vote_review'>
        <div class='vote_name float-left'>{$smarty.section.foo.index} stars</div>
        <div class="rating">
            <div class="full" style='width:{$vote_reviews[$smarty.section.foo.index][1]}%'></div>
        </div>
        <div class='vote_name float-left'>{$vote_reviews[$smarty.section.foo.index][0]|default:0}</div>
    </div>
{/section}
</div>
{/if}

<table id="product_ratings">
    {if $product_rates|@count gt 1}
    <tr class='overal_rating'>
        <td><span class="vote_name">Overal rating</span></td>
        <td>&nbsp;</td>
        <td>{include file='addons/estore_products_review/product_rating.tpl' rating=$product.rating}</td>
    </tr>
    <tr><td>based on {$reviews_navigation.total_items} reviews</td></tr>
    {/if}
    {foreach from=$product_rates item=pr}
    <tr>
        <td><span class="vote_name">{$pr.name}</span></td>
        <td>&nbsp;</td>
        <td>{include file='addons/estore_products_review/product_rating.tpl' rating=$pr.rating}</td>
    </tr>
    {/foreach}
</table>


