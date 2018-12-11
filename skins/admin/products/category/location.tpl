{if $category_location}
<font class="NavigationPath">
{strip}
{section name=position loop=$category_location}
{if $category_location[position].1 ne "" }<a href="{$category_location[position].1|amp}" class="NavigationPath">{/if}
{$category_location[position].0}
{if $category_location[position].1 ne "" }</a>{/if}
{if %position.last% ne "true"}&nbsp;&gt;&nbsp;
{/if}
{/section}
</font>
{/strip}
{/if}
