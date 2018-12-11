{foreach from=$product_video item=video}
{if $video.code}
<div class='product_video'>
<h3 class='product_video_title'>{$video.title}</h3>
<p class='product_video_descr'>{$video.descr}</p>
<div class='product_video_container'>{$video.code}</div>
</div>
{/if}
{/foreach} 
