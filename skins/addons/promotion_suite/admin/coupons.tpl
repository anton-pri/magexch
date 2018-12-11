	{if $ps_coupons}

    	<table width="100%" class="table table-striped dataTable vertical-center">
    	<thead>
        <tr>
        	<th width="10">&nbsp;</th>
        	<th width="15%">{$lng.lbl_coupon}</th>
        	<th width="15%" class="text-center">{$lng.lbl_status}</th>
        	<th width="15%" class="text-center">{$lng.lbl_coupon_disc}</th>
        	<th width="15%" class="text-right">{$lng.lbl_coupon_min}</th>
        	<th width="15%" class="text-center">{$lng.lbl_coupon_times}</th>
        	<th width="25%" class="text-center">{$lng.lbl_coupon_expires}</th>
        </tr>
        </thead>
        {if $ps_type eq 'B'}
            {assign var='ps_name' value='ps_conds'}
            {assign var='ps_obj' value=$ps_conds.B}
        {else}
            {assign var='ps_name' value='ps_bonus'}
            {assign var='ps_obj' value=$ps_bonus.C}
        {/if}
        	<label for='{$ps_name}[{$ps_type}][coupon]' class='error' style='display:none'></label>
		{foreach from=$ps_coupons item=coupon}
    	<tr{cycle values=", class='cycle'"}>
        	<td><input type="radio" name="{$ps_name}[{$ps_type}][coupon]" id='{$ps_name}[{$ps_type}][coupon]' value="{$coupon.coupon}"{if $coupon.coupon eq $ps_obj.coupon} checked="checked"{/if} /></td>
        	<td>{$coupon.coupon}</td>
        	<td align="center">{if $coupon.status eq 1}{$lng.lbl_coupon_active}{elseif $coupon.status eq 2}{$lng.lbl_coupon_disabled}{elseif $coupon.status eq 3}{$lng.lbl_coupon_used}{/if}</td>
        	<td align="center">{if $coupon.coupon_type eq "absolute"}{include file='common/currency.tpl' value=$coupon.discount}{elseif  $coupon.coupon_type eq "percent"}{$coupon.discount|formatprice}%{else}{$lng.lbl_coupon_freeship}{/if}</td>
        	<td align="right">{include file='common/currency.tpl' value=$coupon.minimum}</td>
        	<td align="center">{if $coupon.per_user}{$coupon.times}/{$lng.lbl_coupon_per_user}{else}{$coupon.times_used}/{$coupon.times}{/if}</td>
        	<td align="center" nowrap="nowrap">{$coupon.expire|date_format:$config.Appearance.datetime_format}</td>
        </tr>
		<tr {cycle values=", class='cycle'"}>
            <td>&nbsp;</td>
			<td colspan="6">
		{if $coupon.product_id ne 0}
        {$lng.lbl_coupon_contains_product|substitute:"product_id":$coupon.product_id}
        {elseif $coupon.category_id ne 0}
        {if $addons.simple_mode || 1}
        {if $coupon.recursive eq "Y"}
        {$lng.lbl_coupon_contains_products_cat_rec_href|substitute:"category_id":$coupon.category_id:"path":$catalogs.admin}
        {else}
        {$lng.lbl_coupon_contains_products_cat_href|substitute:"category_id":$coupon.category_id:"path":$catalogs.admin}
        {/if}
        {else}
        {if $coupon.recursive eq "Y"}
        {$lng.lbl_coupon_contains_products_cat_rec|substitute:"category_id":$coupon.category_id}
        {else}
        {$lng.lbl_coupon_contains_products_cat|substitute:"category_id":$coupon.category_id}
        {/if}
        {/if}
        {else}
        {capture name=minamount}{include file='common/currency.tpl' value=$coupon.minimum}{/capture}
        {$lng.lbl_coupon_greater_than|substitute:"amount":$smarty.capture.minamount}
        {/if}
        {if $coupon.coupon_type eq "absolute" and ($coupon.product_id ne 0 or $coupon.category_id ne 0)}
        <br />
        {if $coupon.product_id ne 0}
        {if $coupon.apply_product_once}
        {$lng.lbl_coupon_apply_once}
        {else}
        {$lng.lbl_coupon_apply_each_item}
        {/if}
        {elseif $coupon.category_id ne 0}
        {if $coupon.apply_product_once and $coupon.apply_category_once}
        {$lng.lbl_coupon_apply_once}
        {elseif !$coupon.apply_product_once and !$coupon.apply_category_once}
        {$lng.lbl_coupon_apply_each_item_cat}
        {else}
        {$lng.lbl_coupon_apply_each_title_cat}
        {/if}
        {/if}
		{/if}
			</td>
		</tr>
		{/foreach}
		</table>
	{/if}
