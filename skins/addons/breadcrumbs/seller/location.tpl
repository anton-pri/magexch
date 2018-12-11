{if $location_breadcrumbs}
<div class="navigation">
{strip}
{foreach from=$location_breadcrumbs item=b name='breadcrumb'}
    {if $smarty.foreach.breadcrumb.last && $b.link ne '/index.php'}
        <span class="last">{$b.title}</span>
    {else}
        <a href="{$current_location}/seller{$b.link}">{$b.title}</a>
    {/if}
    {if !$smarty.foreach.breadcrumb.last}<span class="raquo">&raquo;</span>{/if}
{/foreach}
{/strip}
</div>
{/if}
