{* In Ad contentsections skins you must use div with class 'ab_content' and attribute contentsection_id for correct work of counter. See example below *}
<div class='ab_container ab_container_skin_{$cs_skin} ab_container_code_{$service_code}' service_code='{$service_code}' skin='{$cs_skin}'>
{if $cs_skin}
{include file="addons/cms/skins/`$cs_skin`/content.tpl"}
{/if}
</div>
