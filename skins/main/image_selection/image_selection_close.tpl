{capture name=dialog}{strip}
{/strip}{/capture}
<script language="javascript">
function cw_show_image(container, index, alt, img_type, image_x, image_y) {ldelim}
	if (img_type == 'application/x-shockwave-flash')
		text = '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/ flash/swflash.cab#version=6,0,40,0" WIDTH="'+image_x+'" HEIGHT="'+image_y+'" id="flash"> <PARAM NAME=movie VALUE="'+app_web_dir+'/index.php?target=image&type={$type}&id={$id}&tmp=1&imgid='+index+'"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#FFFFFF>	<EMBED src="'+app_web_dir+'/index.php?target=image&type={$type}&id={$id}&tmp=1&imgid='+index+'" quality=high bgcolor=#FFFFFF WIDTH="'+image_x+'" HEIGHT="'+image_y+'" NAME="flash" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">	</EMBED> </OBJECT>';
	else
	    text = '<img src="'+app_web_dir+'/index.php?target=image&type={$type}&id={$id}&tmp=1&imgid='+index+'&timestamp='+{if $file_upload_data.date}{$file_upload_data.date}{elseif $file_upload_data.$id.date}{$file_upload_data.$id.date}{else}$.now(){/if}+'" alt="'+alt+'" /><br/>';
{if $multiple eq 2}
    container.innerHTML = container.innerHTML+text;
{else}
    container.innerHTML = text;
{/if}
{rdelim}
</script>

<script language="javascript">
var container = window.parent.document.getElementById('{$imgid}');
container.innerHTML = '';

{if $multiple eq 2}
{foreach from=$file_upload_data key=index item=item}
cw_show_image(container, '{$index}', '{strip}{include file="main/images/property.tpl" image_data=$item}{/strip}', '{$item.image_type}', '{$item.image_x}', '{$item.image_y}');
{/foreach}
{elseif $multiple eq 1}
cw_show_image(container, '{$id}', '{strip}{include file="main/images/property.tpl" image_data=$file_upload_data image=$file_upload_data.$id}{/strip}', '{$file_upload_data.$id.image_type}', '{$file_upload_data.$id.image_x}', '{$file_upload_data.$id.image_y}');
{else}
cw_show_image(container, '0', '{strip}{include file="main/images/property.tpl" image_data=$file_upload_data}{/strip}', '{$file_upload_data.image_type}', '{$file_upload_data.image_x}', '{$file_upload_data.image_y}');
{/if}
window.parent.hm('image_dialog');
</script>
