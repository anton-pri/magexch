{if $featured_categories}
{if $config.Appearance.mobile_categories_without_image}
<div class="sub">
{foreach from=$featured_categories item=category name=sub}

    
    <a href="{pages_url var='index' cat=$category.category_id}">
        <span class="subcategories {if $smarty.foreach.sub.last}last{/if}">{$category.category}</a>
    </a>


{/foreach}
</div>

{else}
{foreach from=$featured_categories item=category}
<div class="featured_category">
    <div class="f_image"><a href="{pages_url var='index' cat=$category.category_id}">{include file='common/thumbnail.tpl' image=$category.image}</a></div>
    
    <a href="{pages_url var='index' cat=$category.category_id}" class="f_link">{$category.category}</a>
</div>

{/foreach}
{/if}


{/if}