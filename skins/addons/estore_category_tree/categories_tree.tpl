{if $categories}
<div class="categories_left">
{foreach from=$categories item=c name=fcsubcat}
<div class="categories_tree {if $smarty.foreach.fcsubcat.last}last{/if}" id="categories_tree_{$c.category_id}">
    <div class="{if $current_category.category_id eq $c.category_id}sel{elseif $c.subcategory_count eq 0}blank{else}{if $c.selected}minus{else}plus{/if}{/if}" id="categories_tree_{$c.category_id}_img"{if $c.subcategory_count ne 0} onClick="javascript: cw_categories_tree_show_subcategories('{$c.category_id}');"{/if}>&nbsp;</div>
    <a href="{pages_url var='index' cat=$c.category_id}" {if $current_category.category_id eq $c.category_id}class="selected"{/if}>{$c.category}</a>
{if $c.subcategory_count ne 0}
    <div id="categories_tree_subcat_{$c.category_id}">{strip}
{if $c.selected}
{select_categories category_id=$c.category_id current_category_id=$cat assign='subcategories'}
{include file='addons/estore_category_tree/categories.tpl' categories=$subcategories}
{/if}
    {/strip}</div>
{/if}
</div>
{/foreach}
</div>
{/if}