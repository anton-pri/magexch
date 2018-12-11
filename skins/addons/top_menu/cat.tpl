<tr id="{$mid}" {if $item.lev eq 1} class="cycle"{/if}>
{assign var=ajax_id value=$mid}

{if $item.children ne ''}
<script>
children['{$mid}']='{$item.children}';
</script>
{/if}

	<td align="center">{if $item.children ne ''}{*if $item.type eq 'pcat'*}<IMG id="exp-coll-{$mid}" src="{$ic_expd}" border="0" style="cursor:pointer;" {*onclick="javascript: ajaxGet('index.php?target=top_menu&get_menu_subitems={$mid}', null);"*} href="javascript: void(0);" onclick="expand('{$mid}',{$item.lev});">{*else}<IMG id="exp-coll-{$mid}" src="{$ic_coll}" border="0" style="cursor:pointer;" onclick="collapse('{$mid}',null);">{/if*}{/if}</td>
	<td align="center"> {if $item.type eq 'pcat'}<img src='{$ImagesDir}/icon_info_small_grey.gif' alt='info' title='It is real category. You can only hide it.' />{else} <input type="checkbox" onchange="on_drop_item_chng('{$mid}',this);" onclick="this.blur();" id="drop-cb-{$mid}" />{/if}</td>
	<td align="center">{$item.lev}</td>
	<td align="center">{if $item.title_orig ne $item.title}<A href="javascript:;" onclick="restore_title('{$mid}',this);"><img src="{$ic_rest}"></A>{/if}</td>
	<td width="1%"><A href="javascript:edit_row('{$mid}','{$item.type}');" id="sw-jshref-{$mid}"><img src="{$ic_edit}" id="icon-{$mid}" /></A></td>
	<td id="title-{$mid}">
		<span id="title-path-{$mid}">{foreach from=$item.location item=loc}{$loc.category_name} &#187; {/foreach}</span>
		<a id="title-link-{$mid}" href="{$item.link}" target="_blank">
		<span id="title-name-{$mid}">{$item.title}</span>
		</a>
		<input type="hidden" name="otitle" value="{$item.title}" id="title-name-old-{$mid}">
		<input type="hidden" name="ortitle" value="{$item.title_orig}" id="title-name-orig-{$mid}">
		<input type="hidden" name="olink" value="{$item.link}" id="title-link-old-{$mid}">
		<input type="hidden" name="itype" value="{$item.type}" id="item-type-{$mid}">
		<input type="hidden" name="itype" value="{$item.pmid}" id="item-perent-{$mid}">

		<table border="0" width="100%" cellpadding="0" cellspacing="0" style="display:none; width:100%;" id="title-ed-{$mid}">
		<tr>
		<td width="10%" align="left" style="padding:0; valign:middle;">Title:&nbsp;&nbsp;&nbsp;</td>
		<td align="left" style="padding:0; valign:middle;">
		<input type="text" name="title" value="{$item.title}" id="title-name-ed-{$mid}" style="{$inptxt_style}">
		</td>
		</tr>
		{if $item.type eq 'ucat'}
		<tr>
		<td width="10%" align="left" style="padding:0; valign:middle;">Link:&nbsp;&nbsp;&nbsp;</td>
		<td align="left" style="padding:0; valign:middle;">
		<input type="text" name="url" value="{$item.link}" id="title-link-ed-{$mid}" style="{$inptxt_style}">
		</td>
		</tr>
		{/if}
		</table>
	</td>
	<td width="5%">
		<SELECT id="pos-{$mid}" class="selectBox-tiny form-control" onchange="log_updates('{$mid}');" style="width:auto;">
		{section name=ind loop=26}{assign var="i" value=$smarty.section.ind.iteration-1}
		<option value="{$i}"{if $i eq $item.pos} selected{/if}>{$i}</option>
		{/section}
		</SELECT>
	</td>
	<td align="center"><input id="active-{$mid}" type="checkbox"{if $item.active eq 1} checked{/if}{if $active_parent eq 0} disabled{/if} onchange="log_updates('{$mid}');" onclick="this.blur();"></td>
	<td align="center">{if $item.type eq 'ucat'}Link{else}{$item.pcount}{/if}</td>
	{assign var=subcount value=$item.subitems|@count}
	<td>{$subcount}<span id="addnew-{$mid}"> &nbsp;&#187; <A href="#" onclick="add_new_item('{$mid}');">{$lng.lbl_add_new}</A></span></td>
</tr>
{if $item.type eq 'pcat' && $item.children ne ''}
{else}
{if $subcount>0}
{if $active_parent ne 0}
{if $item.active eq 0}{assign var=active_parent value=0}{else}{assign var=active_parent value=1}{/if}
{/if}
{foreach from=$item.subitems key=mid item=item}
{include file="addons/top_menu/cat.tpl"}
{/foreach}
<tr><td colspan="10" align="right"></td></tr>
{/if}
{/if}


