{if $location && !in_array($main,array('profile','orders','document','message_box'))}
{tunnel func='magexch_get_breadcrumbs' via='cw_call' assign='magexch_location'}
{*$magexch_location|@debug_print_var*}
<div class="navigation">
<div class="breadcrumbs">
{strip}
{foreach from=$magexch_location item=loc name='position'}
    {if $loc.1}<a href="{$loc.1|amp}"{if $smarty.foreach.position.last} class="last"{/if}>{else}<span{if $smarty.foreach.position.last} class="last"{else} class="link"{/if}>{/if}
        {if $loc.2}<span class="{$loc.2}">{/if}{$loc.0}{if $loc.2}</span>{/if}
    {if $loc.1}</a>{if !$smarty.foreach.position.last}<span class="location_arrow">&nbsp;>&nbsp;</span>{/if}{else}</span>{/if}
{/foreach}
{/strip}
</div>
</div>
{/if}
