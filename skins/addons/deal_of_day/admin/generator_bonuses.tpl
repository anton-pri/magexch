<script type="text/javascript">
//<!--
var dod_url_get_coupons = "{$dod_url_get_coupons|escape:javascript}";
var dod_url_add_coupon = "{$dod_url_add_coupon|escape:javascript}";

{if $dod_bonus.C.coupon ne ''}
var coupon_code = "{$dod_bonus.C.coupon}";
{else}
var coupon_code = null;
{/if}
//-->
</script>

<div class="box" style="padding-top: 10px;">

{include file='common/subheader.tpl' title=$lng.lbl_dod_bonuses}

<table class="dod-bonuses" width='100%'>


<!-- DISCOUNT -->

<tr id="dod-bonus-discount">
    <td rowspan="2" class="dod-checkbox"><input type="checkbox" name="dod_bonuses[D]" id="dod_bonuses_D" value="1"{if $dod_bonuses.D && !$dod_bonus.D.unused} checked="checked"{/if} /></td>
    <td class="dod-bonus-body"><a class="dod-bonus-picker" href="#" onclick="return false;">{$lng.lbl_dod_bonus_discount}{if $not_sav_bons.D}&nbsp;&nbsp;<span class="dod-not-saved">{$lng.lbl_dod_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="dod-bonus-details" id="dod-bonus-discount-details">
        <label for="dod-discount-value" style="float: left;">{$lng.lbl_dod_discount_value}&nbsp;</label>
        <select name="dod_bonus[D][disctype]" size="1" style="width:auto;">
            <option value="1"{if $dod_bonus.D.disctype eq 1} selected{/if}>{$lng.lbl_dod_dtype_fixed}</option>
            <option value="2"{if $dod_bonus.D.disctype eq 2} selected{/if}>{$lng.lbl_dod_dtype_percent}</option>
        </select>
        <input type="text" name="dod_bonus[D][discount]" value="{$dod_bonus.D.discount}" id="dod-discount-value" size="5" maxlength="17" />
        <div class="clear"></div>

	    <ul class="dod-list">
	        <li><input class="dod-chooser" type="radio" name="dod_bonus[D][apply]" id="dod-discount-cart" value="1"{if $dod_bonus.D.apply eq 1 || $dod_bonus.D.apply eq ''} checked="checked"{/if} /><label class="dod-chooser" for="dod-discount-cart">{$lng.lbl_dod_disc_for_whole_cart}</label></li>
	        <li><input class="dod-chooser" type="radio" name="dod_bonus[D][apply]" id="dod-discount-products" value="3"{if $dod_bonus.D.apply eq 3} checked="checked"{/if} /><label class="dod-chooser" for="dod-discount-products">{$lng.lbl_dod_apply_a_discount_for_deal_of_the_day_product|default:'apply a discount for deal of the day product'}</label></li>
{*	        <li class="dod-entities-block" id="dod-discount-products-always-show-block">
	        </li> *}
	    </ul>
    </div></td>
</tr>


<!-- FREE PRODUCTS -->

<tr id="dod-bonus-forfree">
    <td rowspan="2" class="dod-checkbox"><input type="checkbox" name="dod_bonuses[F]" id='dod_bonuses_F' value="1"{if $dod_bonuses.F} checked="checked"{/if} /></td>
    <td class="dod-bonus-body"><a class="dod-bonus-picker" href="#" onclick="return false;">{$lng.lbl_dod_bonus_forfree}{if $not_sav_bons.F}&nbsp;&nbsp;<span class="dod-not-saved">{$lng.lbl_dod_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="dod-bonus-details" id="dod-bonus-forfree-details">
		<table>
			<tr>
				<th colspan="2">{$lng.lbl_dod_products}</th>
			</tr>
			{product_selector multiple=1 amount_name='quantity' prefix_name='dod_bonus[F][products]' prefix_id='dod_prods2' products=$dod_bonus.F.products}
		</table>

    {if $dod_bonus.F.cats ne '' && $dod_bonus.F.cats|@count gt 1}
    {assign var='_group' value='dod_cats2'}
    <script type="text/javascript">
        {foreach from=$dod_bonus.F.cats item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_catid/, value: '{$_elem.id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catname/, value: '{$_elem.name|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catqty/, value: '{$_elem.quantity|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_link/, value: '0'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
    </div>
    </td>
</tr>


<!-- FREE SHIPPING -->

<tr id="dod-bonus-freeship">
    <td rowspan="2" class="dod-checkbox"><input type="checkbox" name="dod_bonuses[S]" id="dod_bonuses_S" value="1"{if $dod_bonuses.S} checked{/if} /></td>
    <td class="dod-bonus-body"><a class="dod-bonus-picker" href="#" onclick="return false;">{$lng.lbl_dod_bonus_freeship}{if $not_sav_bons.S}&nbsp;&nbsp;<span class="dod-not-saved">{$lng.lbl_dod_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="dod-bonus-details" id="dod-bonus-freeship-details">
        <input type='text' name='dod_bonus[S][discount]'  placeholder='Fixed rate' value="{$dod_bonus.S.discount|default:0}" size='7' />
   		<div class='clear' ></div>
        {include file='main/select/shipping.tpl' name="dod_bonus[S][methods][]" values=$dod_bonus.S.methods multiple=1}
   		<div class='clear' ></div>
	    <ul class="dod-list">
	        <li><input class="dod-chooser" type="radio" name="dod_bonus[S][apply]" id="dod-freeship-cart" value="1"{if $dod_bonus.S.apply eq 1 || $dod_bonus.S.apply eq ''} checked="checked"{/if} /><label class="dod-chooser" for="dod-freeship-cart">{$lng.lbl_dod_disc_for_whole_cart}</label></li>
	        <li><input class="dod-chooser" type="radio" name="dod_bonus[S][apply]" id="dod-freeship-products" value="3"{if $dod_bonus.S.apply eq 3} checked="checked"{/if} /><label class="dod-chooser" for="dod-freeship-products">{$lng.lbl_dod_apply_a_discount_for_deal_of_the_day_product|default:'apply a discount for deal of the day product'}</label></li>
	        <li class="dod-entities-block" id="dod-freeship-products-block">
			<label for='dod_bonus_group_S' class='error' style='display:none'></label>
            <table>
                <tr>
                    <th colspan="2">{$lng.lbl_dod_products}</th>
                </tr>
                {product_selector multiple=1 amount_name='quantity' prefix_name='dod_bonus[S][products]' prefix_id='dod_prods3' products=$dod_bonus.S.products}
            </table>
            <table>
                <tr>
                    <th>{$lng.lbl_dod_categories}</th>
                </tr>
                
                <tr>{assign var='_group' value='dod_cats3'}
                    <td id="{$_group}_box_1">
                        <div id="{$_group}_show_category" style="display:none">
                            <div class="bd"><div class="category_ajax" id="{$_group}_body">{$lng.lbl_loading}</div></div>
                        </div>
                        <input id="{$_group}_catid_0" type="text" size="11" maxlength="11" name="dod_bonus[S][cats][0][id]" value="{$dod_bonus.S.cats[0].id}" />
                        <input id="{$_group}_catname_0" type="text" size="40" maxlength="255" name="dod_bonus[S][cats][0][name]" value="{$dod_bonus.S.cats[0].name|escape}" readonly="readonly" />
                        <input id="{$_group}_catqty_0" type="text" size="11" maxlength="11" name="dod_bonus[S][cats][0][quantity]" value="{$dod_bonus.S.cats[0].quantity|escape}" />
                        <img src="{$ImagesDir}/calendar.jpg" width="22" onclick="dod_show_cats(this, '{$_group}');" id="{$_group}_link_0" />
                    </td>
                    <td id="{$_group}_add_button">{include file="main/multirow_add.tpl" mark=$_group is_lined=true}</td>
                </tr>
                
            </table>
    
    {if $dod_bonus.S.cats ne '' && $dod_bonus.S.cats|@count gt 1}
    {assign var='_group' value='dod_cats3'}
    <script type="text/javascript">
        {foreach from=$dod_bonus.S.cats item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_catid/, value: '{$_elem.id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catname/, value: '{$_elem.name|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catqty/, value: '{$_elem.quantity|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_link/, value: '0'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
	        </li>
    </ul>
    
    
    </div></td>
</tr>
<tr id="dod-bonus-coupon">
    <td rowspan="2" class="dod-checkbox"><input type="checkbox" name="dod_bonuses[C]" id="dod_bonuses_C" value="1"{if $dod_bonuses.C} checked{/if} /></td>
    <td class="dod-bonus-body"><a class="dod-bonus-picker" href="#" onclick="return false;">{$lng.lbl_dod_bonus_coupon}{if $not_sav_bons.C}&nbsp;&nbsp;<span class="dod-not-saved">{$lng.lbl_dod_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="dod-bonus-details" id="dod-bonus-coupon-details">
    <div id="dod-coupons-list">
    {include file="addons/deal_of_day/admin/coupons.tpl" dod_type="C"}
    </div>
    <div style="padding: 10px 0;">
       <input type="button" title="{$lng.lbl_dod_refresh_list}" value="{$lng.lbl_dod_refresh_list}" id="dod-refresh-list" />
	<input type="button" title="{$lng.lbl_dod_new_coupon_title}" value="{$lng.lbl_dod_new_coupon}" onclick="window.open(dod_url_add_coupon);" />
    </div>
    </div></td>
</tr>
</table>

</div>
