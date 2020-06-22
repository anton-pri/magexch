{if $multiple eq 2}
{foreach from=$file_upload_data key=index item=item}
<img 
    src="{$app_web_dir}/index.php?target=image&type={$in_type}&tmp=1&imgid={$index}&timestamp='{if $file_upload_data.date}{$file_upload_data.date}{elseif $file_upload_data.$index.date}{$file_upload_data.$index.date}{/if}" 
    alt="{strip}{include file="main/images/property.tpl" image_data=$item}{/strip}" 
    />
<br/>
{/foreach}
{elseif $multiple eq 1}

{else}

{/if}
