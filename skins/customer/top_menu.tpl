{if $speed_bar}
<div class="speed_bar">
{foreach from=$speed_bar item=sb}
<a href="{eval var=$sb.link}"{if $smarty.foreach.speed_bar.last} id="last"{/if}>{$sb.title}</a>
{/foreach}
</div>
{/if}
