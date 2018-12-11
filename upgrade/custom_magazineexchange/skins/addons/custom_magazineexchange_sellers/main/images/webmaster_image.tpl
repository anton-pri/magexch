{strip}
{select_webmaster_image image=$image assign='web_image'}
{if $web_image}
{if $web_image.image_type eq "application/x-shockwave-flash"}
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="{$web_image.image_x}" HEIGHT="{$web_image.image_y}" id="flash">
<PARAM NAME=movie VALUE="{$web_image.tmbn_url}">
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>

<EMBED src="{$web_image.tmbn_url}" quality=high bgcolor=#FFFFFF WIDTH="{$web_image.image_x}" HEIGHT="{$web_image.image_y}" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
</EMBED>
</OBJECT>
{else}
{if $web_image.link && ($image ne 'logo' || $image_link eq 'Y')}
<a href="{$web_image.link|escape}" title="{$web_image.image_title|escape}">
{/if}
<img src="{$web_image.tmbn_url}" width="{$web_image.image_x}" height="{$web_image.image_y}" alt="{$web_image.alt|escape}" />
{if $web_image.link && ($image ne 'logo' || $image_link eq 'Y')}
</a>
{/if}
{/if}
{/if}
{/strip}
