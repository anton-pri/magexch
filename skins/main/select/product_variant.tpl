<select name="{$name}"{if $class} class="{$class}"{/if}>
{foreach from=$variants item=v key=k}
    <option value="{$k}"{if $value eq $k} selected{/if}>{foreach from=$v.options item=o}{$o.option_name}: {$o.name} {/foreach}</option>
{/foreach}
</select>
