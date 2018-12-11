{if $images ne ""}
{capture name=section}
<center>
{section name=image loop=$images}
{if $images[image].avail eq "Y"}
{if $images[image].tmbn_url}
<img src="{$images[image].tmbn_url}" alt="{$images[image].alt|escape}" style="padding-bottom: 10px;" />
{else}
<img src="{$app_web_dir}/index.php?target=image&id={$images[image].imageid}&amp;type=D" alt="{$images[image].alt|escape}" style="padding-bottom: 10px;" />
{/if}
<br />
{/if}
{/section}
</center>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_detailed_images content=$smarty.capture.section extra='width="100%"'}
{/if}
