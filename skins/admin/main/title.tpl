{strip}
    {section name=position loop=$location step=-1}{$location[position].0|strip_tags|escape}{if not %position.last%} - {/if}{/section}
{/strip}