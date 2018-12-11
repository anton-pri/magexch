{if $config.Appearance.top_categories eq 'Y'}
    {select_categories category_id=0 current_category_id=$cat remove_root=1 assign='categories'}
{else}
    {if $config.category_settings.root_categories eq "Y"}
    {select_categories category_id=0 current_category_id=$cat assign='categories'}
    {else}
    {select_categories category_id=$cat current_category_id=0 assign='categories'}
    {/if}
{/if}

{if $categories}
{capture name=menu}
{if $addons.estore_category_tree}
{include file='addons/estore_category_tree/categories.tpl'}
{else}
<ul>

{foreach from=$categories item=c}
  <li>
    <a href="{pages_url var='index' cat=$c.category_id}">{$c.category}</a>
  </li>
{/foreach}
</ul>
{/if}
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_categories content=$smarty.capture.menu style='categories'}
{/if}
