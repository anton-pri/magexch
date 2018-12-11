{if $addons.bestsellers}
{select_bestsellers category_id=$current_category.category_id assign='bestsellers'}
{if $bestsellers}
{capture name=menu}
<ul>
{if $main eq 'catalog' && $cat}
<b>{$lng.lbl_bestsellers}<br/>
{$lng.lbl_in} {$current_category.category}</b>
{/if}
{foreach from=$bestsellers item=bestseller name='bestseller'}

<div class="bestseller_shadow">
<div class="bestseller_bg">
    <center>{include file='common/thumbnail.tpl' image=$bestseller.image_small}</center>
</div>
</div>
<li><a href="{pages_url var="product" product_id=$bestseller.product_id cat=$cat bestseller=Y}">{$bestseller.product}</a></li>
{/foreach}
</ul>
{/capture}
{ include file='common/menu.tpl' title=$lng.lbl_bestsellers content=$smarty.capture.menu }
{/if}
{/if}
