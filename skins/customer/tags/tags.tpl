{include_once file='customer/tags/tags_js.tpl'}

{if $tags}
    {if $config.Appearance.skin_for_tags_list eq 'line_list'}
        <br>
        {foreach from=$tags item=tag name=itag}
            <span><a style="font-size: {$tag.weight}pt" href="index.php?target=search&mode=search&tag={$tag.name}&new_search">{$tag.name}</a></span>
            {if $smarty.foreach.itag.last}{else},{/if}
        {/foreach}
    {elseif $config.Appearance.skin_for_tags_list eq '2D_canvas'}
        <div id="tags">
            <ul class="weighted">
            {foreach from=$tags item=tag}
                <li><a style="font-size: {$tag.weight}pt" href="index.php?target=search&mode=search&tag={$tag.name}&new_search">{$tag.name}</a></li>
            {/foreach}
            </ul>
        </div>
    {else}
        <div id="myCanvasContainer">
            <canvas width="420px" height="300px" id="myCanvas">
                <p>Anything in here will be replaced on browsers that support the canvas element</p>
            </canvas>
        </div>
        <div id="tags" style="display: none">
            <ul>
            {foreach from=$tags item=tag}
                <li><a style="font-size: {$tag.weight}pt" href="index.php?target=search&mode=search&tag={$tag.name}">{$tag.name}</a></li>
            {/foreach}
            </ul>
        </div>
    {/if}
{/if}
