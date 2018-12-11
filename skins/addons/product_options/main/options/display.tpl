{if $options && $force_product_options_txt eq ''}
{if $is_plain eq 'Y'}
{if $options ne $options_txt}
{foreach from=$options item=v}
   {$v.option_name}: {$v.name}
{/foreach}
{else}
{$options_txt}
{/if}
{else}
{if $options ne $options_txt}
<div class="cart_options">
{foreach from=$options item=v name=options}
	<span class="option">{$v.option_name|default:$v.field}:&nbsp;{$v.name|replace:"\n":"<br>"}</span>
       {if !$smarty.foreach.options.last},{/if}
{/foreach}
</div>
{else}
{$options_txt|replace:"\n":"<br />"}
{/if}
{/if}
{elseif $force_product_options_txt}
{if $is_plain eq 'Y'}
{$options_txt|escape:"html"}
{else}
{$options_txt|replace:"\n":"<br />"}
{/if}
{/if}
