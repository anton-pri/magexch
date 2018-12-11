{foreach from=$profile_sections key=section_name item=section}
{if $section.is_avail && $section_name eq 'basic'}
{include file='common/subheader.tpl' title=$section.section_title}
{include file="main/users/sections/`$section_name`.tpl" read_only=true}
{/if}
{/foreach}
