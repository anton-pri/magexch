<link rel="stylesheet" href="{$SkinDir}/addons/magnifier/general.css" />
<div class="magnifier">

{if $images_count gt 1}
<div>
{foreach from=$zoomer_images item=image}
<a href="index.php?target={$current_target}&product_id={$product_id}&image_id={$image.image_id}"><img src="{$image.tmbn_url}" alt="" width="150" /></a>
{/foreach}
</div>
<script language="javascript">
window.resizeTo('700', '900');
</script>
{/if}

<OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" CODEBASE="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" WIDTH="500" HEIGHT="550" ID="magnifier">
<PARAM NAME="FlashVars" VALUE="zoomifyNavWindow=0&zoomifyImagePath={$image_path}">
<PARAM NAME="MENU" VALUE="FALSE">
<PARAM NAME="SRC" VALUE="{$SkinDir}/addons/magnifier/zoom.swf">
<EMBED FlashVars="zoomifyNavWindow=0&zoomifyImagePath={$image_path}" SRC="{$SkinDir}/addons/magnifier/zoom.swf" MENU="false" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" WIDTH="500" HEIGHT="550" NAME="magnifier" swLiveConnect=true></EMBED>
</OBJECT>
</div>
