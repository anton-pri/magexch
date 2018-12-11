{jstabs}
default_tab="{$js_tab|default:"basic_search"}"
default_template="admin/users/profile.tpl"

{foreach from=$profile_sections key=section_name item=section}
{if $section.is_avail && !($mode == 'add' and $section_name eq 'address')}
[{$section_name}]
title="{$section.section_title}"
{if !$section.is_default}
template="main/users/sections/custom.tpl"
{/if}
{/if}
{/foreach}

{/jstabs}

{include file='tabs/js_tabs.tpl' disabled=1}
