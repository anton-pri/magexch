<form method="post" action="{$current_location}/index.php?target=search" name="articles_products_search_form">
<input type="hidden" name="simple_search" value="Y" />
<input type="hidden" name="action" value="search" />
<input type="hidden" name="posted_data[by_title]" value="Y" />
<input type="hidden" name="posted_data[by_shortdescr]" value="Y" />
<input type="hidden" name="posted_data[by_fulldescr]" value="Y" />
<input type="hidden" name="posted_data[by_manufacturer]" value="Y" />
<input type="hidden" name="posted_data[by_ean]" value="Y" />
<input type="hidden" name="posted_data[by_sku]" value="Y" />
<input type="hidden" name="posted_data[including]" value="all" />
<input type="hidden" name="posted_data[search_in_subcategories]" value="1" />

<div class="minisearch"> 
    <div class="mini-search-text">{$lng.lbl_article_search}:</div>
    <input id="categ_search" type="text" class="" name="posted_data[substring]" size="16" placeholder="{$lng.lbl_keywords}" />
{if $vendorid}
    <select name="posted_data[vendorid1]" >
        <option value="">Items from all Sellers</option>
        <option value="{$vendorid}">Items from this Seller</option>
    </select>
{else}
    <select name="posted_data[category_id]">
     <option value="">All Sections</option>
     {select_categories category_id=$config.custom_magazineexchange.magexch_default_root_category assign='article_search_categories'}
     {foreach from=$article_search_categories item=asc}
     <option value="{$asc.category_id}">{$asc.category}</option>
     {/foreach}
     </select>
{/if}
     <input type="image" src="{$AltImagesDir}/mini_search_button.png" onclick="if(document.getElementById('categ_search').value=='Keywords....') document.getElementById('categ_search').value=''; cw_submit_form('products_search_form');">
</div>
</form>
