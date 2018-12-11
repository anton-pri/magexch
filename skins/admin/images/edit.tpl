{include_once_src file='main/include_js.tpl' src='main/popup_image_selection.js'}
{if $idtag eq ''}{assign var='idtag' value="edit_image_`$in_type`"}{/if}

<div id="{$idtag}">
{if $image.image_type eq 'application/x-shockwave-flash'}
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" id="flash">
<PARAM NAME=movie VALUE="{$image.tmbn_url}">
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>

<EMBED src="{$image.tmbn_url}" quality=high bgcolor=#FFFFFF WIDTH="{$image.image_x}" HEIGHT="{$image.image_y}" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
</EMBED>
</OBJECT>

{elseif $image.tmbn_url}
<img src="{$image.tmbn_url}"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
{else}
<img src="{$catalogs.customer}/index.php?target=image&type={$in_type}&tmp=1{if $image.multiple_tmp && $image.id}&imgid={$image.id}&id={$image.id}{/if}"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
{/if}
</div>

{if !$read_only}
<div class="edit_img">
    <span><input type="button" class="btn btn-minw btn-default btn-green push-5-t" value="{if $button_name}{$button_name}{else}{$lng.lbl_change_image|strip_tags:false|escape}{/if}" onclick='javascript: popup_image_selection("{$image.in_type|default:$in_type}", "{$image.id}", "{$idtag}","{$tabs}");' /></span>
{if $image.image_id && !$no_delete}
&nbsp;&nbsp;
    <span><input id="{$idtag}_delete" class="btn btn-minw btn-green push-5-t"" type="button" value="{$lng.lbl_delete_image|strip_tags:false|escape}" onclick="javascript: {if $delete_js ne ''}{$delete_js|replace:'"':'\"'}{else}self.location='{$delete_url}';{/if}" /></span>
{/if}
</div>
{/if}
