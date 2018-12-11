{tunnel func='cw_web_get_layout_elements' load='web' layout_id=$layout_id layout=$layout assign='elements'}
{if $elements}
{capture name=styles}
{foreach from=$elements key=id item=element}
{if $id}
#{$id} {ldelim}
{if $element.width}
    width: {$element.width}px;
    {if $$element.height}
    height: {$element.height}px;
    {/if}
{else}
    position: absolute;
{/if}
    top: {$element.y|default:0|abs_value}px;
    left: {$element.x|default:0|abs_value}px;
{if $element.display}
    display: {$element.display};
{/if}
{if $element.font}
    font-family: {$element.font};
{/if}
{if $element.font_size}
    font-size: {$element.font_size};
{/if}
{if $element.decoration}
    text-decoration: underline;
{/if}
{if $element.font_weight}
    font-weight: bold;
{/if}
{if $element.font_style}
    font-style: italic;
{/if}
{if $element.color}
    color: {$element.color};
{/if}
{rdelim}
{/if}
{/foreach}
{/capture}
{if $smarty.capture.styles}
<style type="text/css" media="all">
{$smarty.capture.styles}
</style>
{/if}
{/if}
