<script type="text/javascript" language="JavaScript 1.2">
<!--
var images = new Array();
{foreach from=$elements item=v key=k} 
images[{$k}] = new Array({if $v.data_type eq 'application/x-shockwave-flash'}0, 0{else}{$v.elementid}, {$v.data_x|default:"0"}{/if});
{/foreach}
var catalogs_admin = '{$catalogs.admin}';

{literal}
function change_images_width(w) {
var x;
	for(x = 0; x < images.length; x++)
		if(images[x][0] > 0 && images[x][1] > 370)
			document.getElementById('img'+images[x][0]).width = w-8;
}

function zoom_open(id, x, y) {
	return window.open(catalogs_admin+'/index.php?target=banner_element&eid='+id, 'ZOOMIN_POPUP','width='+(x+20)+',height='+(y+20)+',toolbar=no,status=no,scrollbars=no,resizable=yes,menubar=no,location=no,direction=no');
}
{/literal}
-->
</script>

<table cellspacing="2" cellpadding="0" width="100%" class="SectionBox">
{foreach from=$elements item=v} 
<tr>
	<td class="AffiliateElmsBox">
	<table cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td colspan="2" class="AffiliateElmTitle">{$v.elementid}</td> 
	</tr>
	<tr>
		<td class="AffiliateElmIconBox" colspan="2">
<a href="javascript: void(0);" onclick="javascript: zoom_open('{$v.elementid}','{$v.data_x}','{$v.data_y}');">
{if $v.data_type ne 'application/x-shockwave-flash'}
<img vspace="3" id="img{$v.elementid}" src="{$current_location}/index.php?target=banner_element&eid={$v.elementid}"{if $v.data_x gt 370} width="370"{/if} alt="" />
{else}
<img src="{$ImagesDir}/flash_icon1.gif" alt="" />
{/if}
</a>
		</td>
	</tr>
	<tr>
		<td width="50%">
		<table cellspacing="0" cellpadding="0" style="height: 100%;">
	{if $v.data_type ne 'application/x-shockwave-flash'}
		<tr> 
    		<td valign="middle"><a href="javascript: void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#A{$v.elementid}#>';"><img src="{$ImagesDir}/add_obj.gif" alt="{$lng.lbl_add|escape} ({$lng.lbl_clickable|escape})" /></a></td>
			<td valign="middle"><a href="javascript: void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#A{$v.elementid}#>';">{$lng.lbl_add} ({$lng.lbl_clickable})</a></td>
		</tr>
	{/if}
		<tr>
	    	<td valign="middle"><a href="javascript: void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#{$v.elementid}#>';"><img src="{$ImagesDir}/add_obj.gif" alt="{$lng.lbl_add|escape}{if $v.data_type ne 'application/x-shockwave-flash'} ({$lng.lbl_non_clickable|escape}){/if}" /></a></td>
			<td valign="middle"><a href="javascript: void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#{$v.elementid}#>';">{$lng.lbl_add}{if $v.data_type ne 'application/x-shockwave-flash'} ({$lng.lbl_non_clickable}){/if}</a></td>
		</tr>
		<tr> 
			<td valign="middle"><a href="javascript: void(0);" onclick="javascript: zoom_open('{$v.elementid}','{$v.data_x}','{$v.data_y}');"><img src="{$ImagesDir}/zoom.gif" alt="{$lng.lbl_zoom_in|escape}" /></a></td>
			<td valign="middle"><a href="javascript: void(0);" onclick="javascript: zoom_open('{$v.elementid}','{$v.data_x}','{$v.data_y}');">{$lng.lbl_zoom_in}</a></td>
		</tr>
		<tr> 
			<td valign="middle"><a href="index.php?target=salesman_banners&elementid={$v.elementid}&amp;action=delete"><img src="{$ImagesDir}/delete_obj.gif" alt="{$lng.lbl_delete|escape}" /></a></td>
			<td valign="middle"><a href="index.php?target=salesman_banners&elementid={$v.elementid}&amp;action=delete">{$lng.lbl_delete}</a></td>
		</tr>
		</table>
		</td>
		<td valign="top">
		<table cellspacing="0" cellpadding="0">
		<tr>
			<td class="MediaElementProperties">{$lng.lbl_width}:&nbsp;</td>
			<td class="MediaElementProperties">{$v.data_x} px</td>
		</tr>
        <tr>
            <td class="MediaElementProperties">{$lng.lbl_height}:&nbsp;</td>
            <td class="MediaElementProperties">{$v.data_y} px</td>
        </tr>
        <tr>
            <td class="MediaElementProperties">{$lng.lbl_type}:&nbsp;</td>
            <td class="MediaElementProperties">{$v.data_type|regex_replace:"/^[^\/]*\//":""}</td>
        </tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr> 
	</table>
	</td> 
</tr>
{/foreach}
</table>

<script language="javascript">
change_images_width(self.document.documentElement.getElementsByTagName('body')[0].scrollWidth);
</script>
