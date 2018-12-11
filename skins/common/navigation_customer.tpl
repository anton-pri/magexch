

{include file='common/navigation_counter_customer.tpl'}
{assign var="max_nav_pages" value=$config.Appearance.max_nav_pages}
{if $max_nav_pages lt 2}{assign var="max_nav_pages" value=2}{/if}

{if $navigation.total_pages gt 2 || ($navigation.total_items gt 0 && $navigation.objects_per_page gt $max_nav_pages)}
{assign var='navigation_script' value=$navigation.script|amp}
<div class="navigation_pages">
{if $navigation.total_pages gt 1 && $navigation.page lt $navigation.total_pages_minus}
    {math equation="page+1" page=$navigation.page assign='next_page'}
	<a class='next' href="{build_url url=$navigation_script page=$next_page}"></a>
{/if}
{if $navigation.total_pages gt 2}
{*<span>{$lng.lbl_result_pages}:</span>*}

{if $navigation.total_pages le $max_nav_pages+1}
{assign var="stepback" value="1"}
{assign var="stepforward" value="1"}
{assign var="start" value=$navigation.start_page}
{assign var="end" value=$navigation.total_pages}
{else}
{assign var="stepback" value=$max_nav_pages}
{assign var="stepforward" value=$max_nav_pages}
{if $navigation.page le $max_nav_pages}{assign var="stepback" value=$navigation.page-1}{/if}
{if $navigation.page ge $navigation.total_pages-$max_nav_pages}{assign var="stepforward" value=$navigation.total_pages-$navigation.page-1}{/if}
{if $navigation.page le $max_nav_pages/2}{assign var="start" value=1}{else}{assign var="start" value=$navigation.page-$max_nav_pages/2}{/if}
{if $navigation.page ge $navigation.total_pages-$max_nav_pages/2-1}{assign var="end" value=$navigation.total_pages}{else}{assign var="end" value=$navigation.page+$max_nav_pages/2}{/if}
{if $start le 1}{assign var="start" value=1}{assign var="end" value=$max_nav_pages+1}{/if}
{if $end eq $navigation.total_pages}{assign var="start" value=$navigation.total_pages-$max_nav_pages}{/if}
{/if}

{section name=page loop=$end start=$start}
{if %page.first%}
    {if $navigation.page gt 1}
    {math equation="page-1" page=$navigation.page step=$stepback assign='prev_page'}
    <a class='page_arrow left' href="{build_url url=$navigation_script page=$prev_page}"><i class="icon-chevron-left"></i> {$lng.lbl_prev_page|escape}</a>
    {/if}

    {if %page.index% > 1}
    <a href="{build_url url=$navigation_script page=1}" title="{$lng.lbl_page|escape} #1" class="page">1</a>
    {if %page.index% > 2}<span>&nbsp;...&nbsp;</span>{/if}
    {/if}
{/if}

{if %page.index% eq $navigation.page}
<div class="page">{%page.index%}</div>
{else}
<a href="{build_url url=$navigation_script page=%page.index%}" title="{$lng.lbl_page|escape} #{%page.index%}" class="page">{%page.index%}</a>
{/if}

{if %page.last%}
    {if %page.index% < $navigation.total_pages_minus-1}
    {if %page.index% < $navigation.total_pages_minus-2}<span>&nbsp;...&nbsp;</span>{/if}
    <a href="{build_url url=$navigation_script page=$navigation.total_pages_minus}" title="{$lng.lbl_page|escape} #{$navigation.total_pages_minus}" class="page">{$navigation.total_pages_minus}</a>
    {/if}

    {if $navigation.page lt $navigation.total_pages_minus}
    {math equation="page+1" page=$navigation.page step=$stepforward assign='next_page'}
    <a class='page_arrow right' href="{build_url url=$navigation_script page=$next_page}">{$lng.lbl_next_page|escape} <i class="icon-chevron-right"></i></a>
    {/if}
{/if}

{/section}
{/if}

{if $usertype ne 'C'}
<div class="float-right">
<span>{$lng.lbl_items_per_page}:</span>
<select onchange="javascript: window.location.href='{build_url url=$navigation_script items_per_page=""}'+this.value" href='{build_url url=$navigation_script items_per_page=""}'>
{section start=$max_nav_pages loop=$navigation.max_item step=$max_nav_pages name="items_per_page"}
<option value="{$smarty.section.items_per_page.index}"{if $smarty.section.items_per_page.index eq $navigation.objects_per_page} selected{/if}>{$smarty.section.items_per_page.index}</option>
{/section}
</select>
<div class="clear"></div>
</div>
{/if}
<div class="clear"></div>
</div>
{/if}

{assign var='count_view_all' value=$app_config_file.interface.max_count_view_products|default:0}
{if $current_area eq "C" && $count_view_all ne 0}
	<a href="{build_url url=$navigation_script view_all='all'}" title="{$lng.lbl_view_all}" class="page view_all">{$lng.lbl_view_all}</a>
{/if}

