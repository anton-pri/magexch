<ul class="ab_slideshow" style="display:none;">

{foreach from=$contentsections item=contentsection}
  <li>
	<div class='ab_content ab_content_{$contentsection.type} ab_content_skin_{$cs_skin} ab_content_code_{$contentsection.service_code}' contentsection_id='{$contentsection.contentsection_id}'>
		<div style="overflow: hidden; width: 100%;">
			{include file='common/thumbnail.tpl' image=$contentsection.image}
		  </div>
    {if $contentsection.parse_smarty_tags}
      {eval var=$contentsection.content}
    {else}
      {$contentsection.content}
    {/if}
	</div>
  </li>
{/foreach}
</ul>
