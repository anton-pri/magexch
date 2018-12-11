{capture name="section"}
{*include file='common/page_title.tpl' title=$lng.lbl_memberships*}
<div class="block">
  <div class="block-content block-content-full">
	<div class="dialog_title">{$lng.txt_edit_membership_levels_top_text}</div>
  </div>
</div>

{if $smarty.get.mode neq 'add'}

{include file='main/select/edit_lng.tpl' script='index.php?target=memberships'}

{foreach from=$memberships key=type item=v}
{include file='admin/memberships/membership_edit.tpl' type=$type levels=$v title=$memberships_lbls.$type}
{/foreach}

{else}

{include file='admin/memberships/membership_add.tpl' type=$smarty.get.type}

{/if}

{/capture}
{include file='admin/wrappers/section.tpl' content=$smarty.capture.section title=$lng.lbl_memberships}
