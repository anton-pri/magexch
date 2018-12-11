{if $no_root eq ''}
<div class="ajax_category_first_level_disabled" id="{$id}_0">
        <img src="{$ImagesDir}/minus.gif" onclick="javascript:cw_ajax_show_subcategories('{$id}', 'subcat_{$id}_0', '0', '0', this, '{$index}');" />

    <a href="javascript: cw_select_categories('{$id}', '0', '{$lng.lbl_root_level}', '{$index}')">{$lng.lbl_root_level}</a>
    <div class="ajax_category_second_level" id="subcat_{$id}_0">
{/if}
{if $categories}
{strip}
{foreach from=$categories item=c name=subcat}
{assign var='cat_id' value=$c.category_id}
<div class="ajax_category_first_level{if !$c.avail}_disabled{/if}{if $c.category_id eq $current_category} selected{/if}" id="{$id}_{$cat_id}">
{if $c.subcategory_count}
<img src="{$ImagesDir}/{if $c.selected}minus{else}plus{/if}.gif" onclick="javascript:cw_ajax_show_subcategories('{$id}', 'subcat_{$id}_{$cat_id}', '{$cat_id}', '{$current_category}', this, '{$index}');" />
{else}
<img src="{$ImagesDir}/spacer.gif" width="12px" />
{/if}
<a href="javascript: cw_select_categories('{$id}', '{$c.category_id}', '{$c.category|escape:clear}', '{$index}')">{$c.category}</a>
    <div class="ajax_category_second_level" id="subcat_{$id}_{$cat_id}">{strip}
{if $c.selected}
{select_categories category_id=$cat_id current_category_id=$value assign="subcategories"}
{include file='categories_ajax/category_ajax_incl.tpl' categories=$subcategories no_root=true}
{/if}
    {/strip}</div>
</div>
{/foreach}
{/strip}
{/if}
{if $no_root eq ''}
</div>
 </div>
{/if}
