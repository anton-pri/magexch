{* Obsolete. We do not use page-specific settings anymore *}

{if $settings_categories}
<div class="settings_top">
{foreach from=$settings_categories item=settings_category}
{capture name=settings}
<form action="index.php?target={$current_target}" method="post" name="configuration_form_{$settings_category.category}">
<input type="hidden" name="action" value="update_settings" />
<input type="hidden" name="category" value="{$settings_category.category}" />
<input type="hidden" name="l_redirect" value="{$smarty.server.REQUEST_URI}" />

{include file='main/settings/settings_table.tpl' included_tab=$settings_category.category}
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form(document.configuration_form_`$settings_category.category`)"}

</form>
{/capture}
{include file='common/section.tpl' title=$settings_category.title content=$smarty.capture.settings hidden=true style='simple'}
{/foreach}
</div>
{/if}
