{if $location_breadcrumbs}
<ol class="breadcrumb push-10-t">
{strip}
{foreach from=$location_breadcrumbs item=b name='breadcrumb'}
<li>
    {if $smarty.foreach.breadcrumb.last && $b.link ne '/index.php'}
        <span class="last">{$b.title}</span>
    {else}
        <a href="{$current_location}/{$smarty.const.APP_AREA}{$b.link}">{$b.title}</a>
    {/if}
</li>
{/foreach}
{/strip}
</ol>
{/if}
