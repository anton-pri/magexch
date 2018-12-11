{capture name=section}

{jstabs}
default_tab={$cat|default:"General"}
default_template="main/settings/settings_table.tpl"

[submit]
title="{$lng.lbl_save}"
style="btn-green "
href="javascript: cw_submit_form('settings_form');"

{foreach from=$categories item=option}
[{$option.category}]
title="{$option.title|escape:json}"
{/foreach}

{/jstabs}
{capture name=block}
<script type="text/javascript" src="{$SkinDir}/js/settings.js"></script>
<form name="settings_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="cat" id="form_js_tab" value="{$cat}">
<input type="hidden" name="action" value="update" />
	{if $config_category.addon || $config_category.is_local}
        {include file='main/settings/settings_table.tpl' included_tab=$cat}
        <div class="buttons">
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('settings_form')" acl='__2501' style="btn-green push-20"}
        </div>
    {else}
        <div class="block general-settings">
        {include file='admin/tabs/js_tabs.tpl'}
        </div>
    {/if}
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}

{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_settings}
