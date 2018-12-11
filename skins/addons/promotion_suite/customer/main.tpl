{if $ps_offers ne '' && $ps_offers|@count > 0}
<div class="ps-offers">
{foreach from=$ps_offers item='ps_offer'}
	<div class="ps-offer-inner">
    	<div class="ps-offer-image">
        {if $ps_offer.img.tmbn_url}
        	<img src="{$ps_offer.img.tmbn_url}" alt="{$ps_offer.img.alt|escape}" />
        {else}
        	<img src="{$app_web_dir}/index.php?target=image&id={$ps_offer.img.imageid}&amp;type={$ps_img_type}" alt="{$ps_offer.img.alt|escape}" />
        {/if}
        </div>
    	<dl class="ps-offer-details" style="margin-left: {$ps_image_width+10}px;">
    		<dt>{$ps_offer.title}<div class="ps-offer-date">{$ps_offer.enddate|date_format:$config.Appearance.date_format}</div></dt>
    		<dd>{eval var=$ps_offer.description}</dd>
    	</dl>
    	<div class="clear"></div>
	</div>
{/foreach}
</div>
{else}
{$lng.msg_ps_no_offers}
{/if}