<div class="products_top">
<div class="nav_line">

<!-- cw@sort [ -->
<div class="float-left sort_by">
{if $sort_fields}
<div class="sort_order">
<span class="sort_lng">{$lng.lbl_sort_by}</span>
<select name="sort" onchange="cw_pf_load('{build_url url=$navigation.script  force_sign=true sort=null sort_direction=null }sort='+this.value);" class="sort">
{foreach from=$sort_fields key=name item=field}
    <option value="{$name}&sort_direction=0" {if $search_prefilled.sort_field eq $name && !$search_prefilled.sort_direction}selected{/if}>{$lng.lbl_sort_by} {$field} &uarr;</option>
    <option value="{$name}&sort_direction=1" {if $search_prefilled.sort_field eq $name && $search_prefilled.sort_direction}selected{/if}>{$lng.lbl_sort_by} {$field} &darr;</option>
{/foreach}
</select>
{/if}
</div>
<!-- cw@sort ] -->

<!-- cw@per_page [ -->
{if !$search_prefilled.all && $config.Appearance.infinite_scroll ne 'Y'}
<span class="sort_lng">{$lng.lbl_show}</span>
<select name="per_page" onchange="cw_pf_load('{build_url url=$navigation.script force_sign=true items_per_page=null}items_per_page='+this.value);" class="per_page">
{foreach from=$app_config_file.interface.items_per_page item=ipp}
    <option value="{$ipp}"{if $ipp eq $navigation.objects_per_page} selected{/if}>{$ipp}</option>
{/foreach}
    {assign var='count_view_all' value=$app_config_file.interface.max_count_view_products|default:0}
    {if $current_area eq "C" && $count_view_all ne 0}
    	<option value="view_all=all" {if $navigation.objects_per_page eq $count_view_all}selected{/if}>{$lng.lbl_view_all}</option>
    {/if}
</select>
<span class="per_page">{$lng.lbl_per_page}</span>
{/if}
<!-- cw@per_page ] -->

</div>

<div class="category_nav">
<!-- cw@filter_results [ -->
  <div class="filter">
  {if $product_filter_navigation}
    <span class="selection">{$lng.lbl_your_selections}:</span>
    {foreach from=$product_filter_navigation item=filter}
        <a href="{$filter.1}" class="{$filter.2}">{$filter.0}</a>
    {/foreach}
  {/if}
  </div>
<!-- cw@filter_results ] -->

<!-- cw@list_views [ -->

    <div class="tabs">
{math equation="set_view==0" set_view=$set_view assign='selected'}
{build_url url=$navigation.script set_view=0 assign='set_view_url'}
{include file='tabs/section_tab.tpl' href="javascript: void(0);" onclick="cw_pf_load('`$set_view_url`');" title=$lng.lbl_standart_view_icon class='standart'}
{math equation="set_view==1" set_view=$set_view assign='selected'}
{build_url url=$navigation.script set_view=1 assign='set_view_url'}
{include file='tabs/section_tab.tpl' href="javascript: void(0);" onclick="cw_pf_load('`$set_view_url`');" title=$lng.lbl_gallery_view_icon class='gallery'}
{math equation="set_view==2" set_view=$set_view assign='selected'}
{build_url url=$navigation.script set_view=2 assign='set_view_url'}
{include file='tabs/section_tab.tpl' href="javascript: void(0);" onclick="cw_pf_load('`$set_view_url`');" title=$lng.lbl_compact_view_icon class='compact'}
    </div>
    <div class="tabs_view">{$lng.lbl_view}:</div>
<!-- cw@list_views ] -->

</div>

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

<!-- cw@top_pagination [ -->
<div class="top_navigation">
  {include file='common/navigation_customer.tpl'}
</div>
<!-- cw@top_pagination ] -->

<div class="clear"></div>
