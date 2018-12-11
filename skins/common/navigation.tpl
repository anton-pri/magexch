{assign var="max_nav_pages" value=$config.Appearance.max_nav_pages}
{if $max_nav_pages lt 2}{assign var="max_nav_pages" value=2}{/if}

{if $navigation.total_pages gt 2 || ($navigation.total_items gt 0 && $navigation.objects_per_page gt $max_nav_pages)}
{assign var='navigation_script' value=$navigation.script|amp}
<div class="dataTables_paginate paging_simple_numbers">
{if $navigation.total_pages gt 2}

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
{if $start lt 1}{assign var="start" value=1}{/if}
{if $start eq 1}{assign var="end" value=$max_nav_pages+1}{/if}
{if $end eq $navigation.total_pages}{assign var="start" value=$navigation.total_pages-$max_nav_pages}{/if}
{/if}
<ul class="pagination">
  {section name=page loop=$end start=$start}
   
    {if %page.first%}
      {if $navigation.page gt 1}
        <li class="paginate_button"><a class='page_arrow' href="{$navigation_script}{$navigation.page_prefix}{if $navigation.objects_per_page gt 0}&items_per_page={$navigation.objects_per_page}{/if}&page={math equation="page-step" page=$navigation.page step=$stepback}"><i class="fa fa-angle-left"></i></a></li>
      {/if}
    {/if}
    {if %page.index% eq $navigation.page}
      <li class="paginate_button active"><a>{%page.index%}</a></li>
    {else}
      <li class="paginate_button"><a href="{$navigation_script}{$navigation.page_prefix}{if $navigation.objects_per_page gt 0}&items_per_page={$navigation.objects_per_page}{/if}&page={%page.index%}" title="{$lng.lbl_page|escape} #{%page.index%}" class="page">{%page.index%}</a></li>
    {/if}
    {if %page.last%}
      {assign var='count_view_all' value=$app_config_file.interface.max_count_view_products|default:0}
      {if $current_area eq "C" && $count_view_all ne 0}
	  <li class="paginate_button view_all"><a href="{build_url url=$navigation_script view_all='all'}" title="{$lng.lbl_view_all}" class="page">{$lng.lbl_view_all}</a></li>
      {/if}

      {if $navigation.page lt $navigation.total_pages_minus}
        <li class="paginate_button"><a class='page_arrow' href="{$navigation_script}{$navigation.page_prefix}{if $navigation.objects_per_page gt 0}&items_per_page={$navigation.objects_per_page}{/if}&page={math equation="page+step" page=$navigation.page step=$stepforward}"><i class="fa fa-angle-right"></i></a></li>
      {/if}
    {/if}
  {/section}
</ul>
{/if}



<div class="clear"></div>

</div>
{/if}
