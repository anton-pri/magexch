{*** In Ad contentsections skins you must use div with class 'ab_content' and attribute contentsection_id for correct work of counter. See example below ***}
{foreach from=$contentsections item=contentsection}
<div class='ab_content ab_content_{$contentsection.type} ab_content_skin_{$cs_skin} ab_content_code_{$contentsection.service_code}' contentsection_id='{$contentsection.contentsection_id}'>
{if $contentsection.attributes ne ''}
{foreach from=$contentsection.attributes item=attr}
{$attr.attribute_data.name}:&nbsp;{foreach from=$attr.values item=option name=attr_options}{$option.value}{if !$smarty.foreach.attr_options.last}, {/if}{/foreach}
<br />
{/foreach}
{/if}
{if $contentsection.parse_smarty_tags}
{eval var=$contentsection.content}
{else}
{$contentsection.content}
{/if}
{if $is_editable}{include file='addons/cms/content_ext.tpl'}{/if}
{if $contentsection.type eq 'html'}
{webmaster type='cms' key=$contentsection.contentsection_id}
{elseif $contentsection.type eq 'image'}
{webmaster type='cms_images' key=$contentsection.contentsection_id extra='style="position:absolute; top:15px; right:15px; background-color:white !important;"'}
{/if}
</div>
{/foreach}
<div class='clear'></div>
