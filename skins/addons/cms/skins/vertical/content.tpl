{*** In Ad contentsections skins you must use div with class 'ab_content' and attribute contentsection_id for correct work of counter. See example below ***}
{foreach from=$contentsections item=contentsection}
{if $contentsection.type eq 'staticpage' or $contentsection.type eq 'staticpopup'}
  {if $preload_popup eq 'Y' && $contentsection.type eq 'staticpopup'}
    {assign var="_contentsection_url" value="#preloaded_staticpopup_`$contentsection.contentsection_id`"}
  {else} 
    {capture assign="_contentsection_url"}{pages_url var="pages" page_id=$contentsection.contentsection_id}{/capture}
  {/if}
{else}
  {assign var="_contentsection_url" value=$contentsection.url}
{/if}
<div class='ab_content ab_content_{$contentsection.type} ab_content_skin_{$cs_skin} ab_content_code_{$contentsection.service_code}' contentsection_id='{$contentsection.contentsection_id}'>
{if $_contentsection_url}<a href='{$_contentsection_url}' {if $contentsection.type ne 'staticpopup'}target='{$contentsection.target}'{/if} rel="cms_link_{$contentsection.type}{if $preload_popup eq 'Y' && $contentsection.type eq 'staticpopup'}_preload{/if}" title="{$contentsection.name|strip_tags|escape:javascript}">{/if}
{if $contentsection.type eq 'image'}
{include file='common/thumbnail.tpl' image=$contentsection.image}
{elseif $contentsection.type eq 'html'}
{if $contentsection.parse_smarty_tags}
{eval var=$contentsection.content}
{else}
{$contentsection.content}
{/if}
{elseif $contentsection.type eq 'staticpage' or $contentsection.type eq 'staticpopup'}
{if $page_link_override ne ''}{$page_link_override}{else}{$contentsection.name}{/if} 
{/if}
{if $_contentsection_url}</a>{/if}
{if $is_editable}{include file='addons/cms/content_ext.tpl'}{/if}
{if $contentsection.type eq 'html'}
{webmaster type='cms' key=$contentsection.contentsection_id}
{elseif $contentsection.type eq 'image'}
{webmaster type='cms_images' key=$contentsection.contentsection_id extra='style="position:absolute; top:15px; right:15px; background-color:white !important;"'}
{/if}
</div>
{if $preload_popup eq 'Y' && $contentsection.type eq 'staticpopup'}<div id="preloaded_staticpopup_{$contentsection.contentsection_id}" style="display:none;">{include file='addons/cms/customer/display_static_content.tpl' page_data=$contentsection}</div>{/if}
{/foreach}
<div class='clear'></div>
