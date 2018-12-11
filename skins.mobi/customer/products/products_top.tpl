<div class="products_top">
<div class="category_nav">
  <div class="filter">
  {if $product_filter_navigation}
    <span class="selection">{$lng.lbl_your_selections}:</span>
    {foreach from=$product_filter_navigation item=filter}
        <a href="{$filter.1}" class="{$filter.2}">{$filter.0}</a>
    {/foreach}
  {/if}
  </div>

</div>

<div class="nav_line">
<div class="float-left sort_by">
<span class="sort_lng">{$lng.lbl_sort_by}:</span>


{if $sort_fields}
<select name="sort" onchange="cw_pf_load('{build_url url=$navigation.script  force_sign=true sort=null sort_direction=null }sort='+this.value);" class="sort">
{foreach from=$sort_fields key=name item=field}
    <option value="{$name}&sort_direction=0" {if $search_prefilled.sort_field eq $name && !$search_prefilled.sort_direction}selected{/if}>{$lng.lbl_sort_by} {$field} &uarr;</option>
    <option value="{$name}&sort_direction=1" {if $search_prefilled.sort_field eq $name && $search_prefilled.sort_direction}selected{/if}>{$lng.lbl_sort_by} {$field} &darr;</option>
{/foreach}
</select>
{/if}

{if !$search_prefilled.all}
<select name="per_page" onchange="cw_pf_load('{build_url url=$navigation.script force_sign=true items_per_page=null}items_per_page='+this.value);" class="per_page">
    <option value="items_per_page=10" {if $navigation.objects_per_page eq 10}selected{/if}>10</option>
    <option value="items_per_page=15" {if $navigation.objects_per_page eq 15}selected{/if}>15</option>
    <option value="items_per_page=20" {if $navigation.objects_per_page eq 20}selected{/if}>20</option>
    <option value="items_per_page=25" {if $navigation.objects_per_page eq 25}selected{/if}>25</option>
    <option value="items_per_page=30" {if $navigation.objects_per_page eq 30}selected{/if}>30</option>
    {assign var='count_view_all' value=$app_config_file.interface.max_count_view_products|default:0}
    {if $current_area eq "C" && $count_view_all ne 0}
    	<option value="view_all=all" {if $navigation.objects_per_page eq $count_view_all}selected{/if}>{$lng.lbl_view_all}</option>
    {/if}
</select>
{/if}

</div>
    {include file='common/navigation_customer.tpl'}
</div>

{if $product_manufacturers}
<select name="manufacturer" onchange="javascript:document.location.href='{build_url url=$navigation.script manufacturer=''}'+this.value;" class="manufacturer">
    <option value="">{$lng.lbl_select_manufacturer}</option>
{foreach from=$product_manufacturers key=name item=manufacturer}
    <option value="{$manufacturer.manufacturer_id}" {if $manufacturer.selected} selected="selected"{/if}>{$manufacturer.manufacturer}</option>
{/foreach}
</select>
{/if}
</div>
<div class="clear"></div>
