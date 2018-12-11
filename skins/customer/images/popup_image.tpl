<script type="text/javascript">
<!--
var images = [];
{foreach from=$images key=k item=v name=dimage}
images[{$k}] = [new Image(),'{$v.alt|escape:"javascript"}', '{$v.tmbn_url}', false];
{/foreach}

var added_h = {if $images_count > 1}150{else}120{/if};
var larrow_grey = false;
var rarrow_grey = false;
-->
</script>

{include_once_src file='main/include_js.tpl' src='customer/images/popup_image_js.js'}

{if !$page}{assign var="page" value=1}{/if}
{math assign="idx" equation="x-1" x=$page}

{if $images_count > 0}

<script type="text/javascript">
<!--
larrow = new Image();
larrow.src = '{$ImagesDir}/larrow.gif';
rarrow = new Image();
rarrow.src = '{$ImagesDir}/rarrow.gif';

larrow_grey = new Image();
larrow_grey.src = '{$ImagesDir}/larrow_grey.gif';
rarrow_grey = new Image();
rarrow_grey.src = '{$ImagesDir}/rarrow_grey.gif';

larrow2 = new Image();
larrow2.src = '{$ImagesDir}/larrow_2.gif';
rarrow2 = new Image();
rarrow2.src = '{$ImagesDir}/rarrow_2.gif';

larrow2_grey = new Image();
larrow2_grey.src = '{$ImagesDir}/larrow_2_grey.gif';
rarrow2_grey = new Image();
rarrow2_grey.src = '{$ImagesDir}/rarrow_2_grey.gif';

var max_nav_pages = {$config.Appearance.max_nav_pages|default:0};
var lbl_page = "{$lng.lbl_page|escape:javascript}";
var spc = new Image();
spc = '{$ImagesDir}/spacer.gif';

var max_x = '{$max_x}';
{literal}
$(document).ready(function() {

	if (max_x && max_x != '0') {
		// if image width more then screen width
		if (max_x > $(window).width()) {
			max_x = $(window).width();
		}
		
		$('#main_area').css('width', max_x + 'px');
	}

	// hide paging if only one page
	if (images.length <= 1) {
		$('#image_table').css('display', 'none');
	}
});
{/literal}
--></script>

<table cellpadding="0" id="image_table">
<tr>
{if $config.Appearance.max_nav_pages > 0 && $images_count > $config.Appearance.max_nav_pages}
	<td><a href="javascript: void(0);" onclick="javascript: changeImg(current_id-max_nav_pages);"><img id="larr2" src="{$ImagesDir}/larrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_prev_group_pages|escape}" /></a></td>
{/if}
	<td valign="middle"><a href="javascript: void(0);" onclick="javascript: changeImg(current_id-1);"><img id="larr" src="{$ImagesDir}/larrow.gif" class="NavigationArrow" alt="{$lng.lbl_prev_page|escape}" /></a>&nbsp;</td>
	<td id="prow"></td>
	<td valign="middle">&nbsp;<a href="javascript: void(0);" onclick="javascript: changeImg(current_id+1);"><img id="rarr" src="{$ImagesDir}/rarrow.gif" class="NavigationArrow" alt="{$lng.lbl_next_page|escape}" /></a></td>
{if $config.Appearance.max_nav_pages > 0 && $images_count > $config.Appearance.max_nav_pages}
	<td><a href="javascript: void(0);" onclick="javascript: changeImg(current_id+max_nav_pages);"><img id="rarr2" src="{$ImagesDir}/rarrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_next_group_pages|escape}" /></a></td>
{/if}
</tr>
</table>
{/if}

<img id="img" alt="" src="{$ImagesDir}/spacer.gif" />
<br/>

<a href="javascript: void(0);" onclick="javascript: window.close();">{$lng.lbl_close_window}</a>
