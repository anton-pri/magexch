{if $image.image_type eq "application/x-shockwave-flash"}<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" id="{if $id ne ''}{$id}{/if}">
<PARAM NAME=movie VALUE="{$image.tmbn_url}">
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>

<EMBED src="{$image.tmbn_url}" quality=high bgcolor=#FFFFFF WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
</EMBED>
</OBJECT>
{else}
{product_image image=$image image_type=$image_type product_id=$product_id class=$class id=$id width=$width html_width=$html_width height=$height html_height=$html_height}
{/if}
