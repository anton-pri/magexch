{if $image.image_type eq "application/x-shockwave-flash"}<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" id="{if $id ne ''}{$id}{/if}">
<PARAM NAME=movie VALUE="{$image.tmbn_url}">
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>

<EMBED src="{$image.tmbn_url}" quality=high bgcolor=#FFFFFF WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
</EMBED>
</OBJECT>
{else}
<img{if $id ne ''} id="{$id}"{/if} src="{$image.tmbn_url}"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="common/image_alt.tpl" image=$image image_type=$image_type alt=$image.alt}"{if $class} class="{$class}"{/if} />
{/if}
