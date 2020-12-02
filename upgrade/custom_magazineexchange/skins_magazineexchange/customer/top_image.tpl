{if $alt_template eq "plans_section"}
<img src="{$AltImagesDir}/Plans_Section_Header.png" width="950" height="84">
{elseif $alt_template eq "new_seller"}
<img src="{$AltImagesDir}/New_Sellers_Section_Header.png" width="950" height="84">
{elseif $alt_template eq "subscribtion"}
<div class="CategoryTop" style="background: url({$AltImagesDir}/SubHub_Section_Header.png) top left no-repeat; width=" 950"="" height="84"><div class="PageAvatar"><img src="{$AltImagesDir}/Magazine_Page_Avatar_Blue.gif" width="190" height="78"></div></div>
{else}
{include file="customer/category_image.tpl"}
{/if}
