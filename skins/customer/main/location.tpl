{if $location}
<div class="navigation">
<div class="breadcrumbs">
{strip}
{foreach from=$location item=loc name='position'}
    {if $loc.1}<a href="{$loc.1|amp}"{if $smarty.foreach.position.last} class="last"{/if}>{else}<span{if $smarty.foreach.position.last} class="last"{else} class="link"{/if}>{/if}
        {if $loc.2}<span class="{$loc.2}">{/if}{$loc.0}{if $loc.2}</span>{/if}
    {if $loc.1}</a>{else}</span>{/if}
{/foreach}
{/strip}
</div>
</div>
{/if}
