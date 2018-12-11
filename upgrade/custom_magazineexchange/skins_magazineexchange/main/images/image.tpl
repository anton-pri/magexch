{if $image.image_type eq "application/x-shockwave-flash"}<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" id="flash">
<PARAM NAME=movie VALUE="{if $image.tmbn_url}{$image.tmbn_url}{else}{if $full_url}{$catalogs.customer}{else}{$app_web_dir}{/if}/index.php?target=image&amp;type={$image.in_type}&id={$image.id}{/if}">
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>

<EMBED src="{if $image.tmbn_url}{$image.tmbn_url}{else}{if $full_url}{$catalogs.customer}{else}{$app_web_dir}{/if}/index.php?target=image&amp;type={$image.in_type}&id={$image.id}{/if}" quality=high bgcolor=#FFFFFF WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
</EMBED>
</OBJECT>{else}<img src="{if $image.tmbn_url}{$image.tmbn_url}{else}{if $full_url}{$catalogs.customer}{else}{$app_web_dir}{/if}/index.php?target=image&amp;type={$image.in_type}&id={$image.id}{/if}"{if $image.image_x} width="{$image.image_x}"{/if}{if $image.image_y} height="{$image.image_y}"{/if} alt="{$image.alt|escape}" />
{/if}
