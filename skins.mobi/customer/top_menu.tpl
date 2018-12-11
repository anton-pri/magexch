
<div class="speed_bar">
{if $speed_bar}
{foreach from=$speed_bar item=sb name='speed_bar'}
<a href="{eval var=$sb.link}"{if $smarty.foreach.speed_bar.last} id="last"{/if}>{$sb.title}</a>
{/foreach}
{/if}

<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="index.php?target=cart">Cart</a></li>
</ul>
</div>

