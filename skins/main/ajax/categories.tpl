{strip}
{if $return_type eq 'html'}
{include file="categories_ajax/category_ajax_incl.tpl" categories=$subcategories}
{else}
{ldelim}
"el_name":"{$el_name|escape:"json"}",
"parent_category_id":"{$parent_category_id|escape:"json"}",
"categories_count":"{count value=$subcategories}",
{if $el_name}
"categories_categories_tree":"{capture name=sub}{include file="categories_ajax/category_ajax_incl.tpl" categories=$subcategories}{/capture}{$smarty.capture.sub|escape:"json"}",
{else}
"categories_categories_tree":"{capture name=sub}{include file="addons/estore_category_tree/categories_tree.tpl" categories=$subcategories}{/capture}{$smarty.capture.sub|escape:"json"}",
{/if}
"categories":{ldelim}
{foreach from=$subcategories item=category name="categories"}
    "{$category.category_id}":{ldelim}
        "category":"{$category.category|escape:"json"}"
    {rdelim}{if !$smarty.foreach.categories.last},{/if}
{/foreach}
{rdelim}
{rdelim}
{/if}
{/strip}
