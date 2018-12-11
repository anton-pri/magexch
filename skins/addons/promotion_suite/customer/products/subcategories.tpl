{if $ps_featured_offer ne '' && $ps_featured_offer|@count > 0}
<div class="ps-featured-offer">
	<div class="ps-offer-inner">
    	<div class="ps-offer-image">
        {if $ps_featured_offer.img.tmbn_url}
        	<img src="{$ps_featured_offer.img.tmbn_url}" alt="{$ps_featured_offer.img.alt|escape}" />
        {else}
        	<img src="{$app_web_dir}/index.php?target=image&id={$ps_featured_offer.img.imageid}&amp;type={$ps_img_type}" alt="{$ps_featured_offer.img.alt|escape}" />
        {/if}
        </div>
    	<dl class="ps-offer-details" style="margin-left: {$ps_image_width+10}px;">
    		<dt>{$ps_featured_offer.title}<div class="ps-offer-date">{$ps_featured_offer.enddate|date_format:$config.Appearance.date_format}</div></dt>
    		<dd>{eval var=$ps_featured_offer.description}</dd>
    	</dl>
    	<div class="clear"></div>
	</div>
</div>
{/if}