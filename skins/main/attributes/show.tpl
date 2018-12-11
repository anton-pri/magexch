{if $attribute.type eq 'date'}{$attribute.value|date_format:$config.Appearance.date_format}
{elseif $attribute.type eq 'yes_no'}{include file='main/select/yes_no.tpl' value=$attribute.value is_text=1}
{else}
    {foreach from=$attribute.values_str item=value}
        {if $attribute.images[$value]}
        {include file='common/thumbnail.tpl' image=$attribute.images[$value]}
        {else}
        {$value}
        {/if}
    {/foreach}
{/if}
