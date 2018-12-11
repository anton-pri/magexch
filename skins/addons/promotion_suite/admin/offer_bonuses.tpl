<div class="box">
{include file='common/subheader.tpl' title=$lng.lbl_ps_bonuses}

<script type="text/javascript">
//<!--
var ps_url_get_coupons = "{$ps_url_get_coupons|escape:javascript}";
var ps_url_add_coupon = "{$ps_url_add_coupon|escape:javascript}";

{if $ps_bonus.C.coupon ne ''}
var coupon_code = "{$ps_bonus.C.coupon}";
{else}
var coupon_code = null;
{/if}
//-->
</script>

<table class="ps-bonuses" width='100%'>


<!-- DISCOUNT -->

<tr id="ps-bonus-discount">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_bonuses[D]" id="ps_bonuses_D" value="1"{if $ps_bonuses.D} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_bonus_discount}{if $not_sav_bons.D}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details form-inline" id="ps-bonus-discount-details">
        <label for="ps-discount-value">{$lng.lbl_ps_discount_value}&nbsp;</label>
        <div class="form-group">
          <select name="ps_bonus[D][disctype]" size="1"  class="form-control">
            <option value="1"{if $ps_bonus.D.disctype == 1} selected{/if}>{$lng.lbl_ps_dtype_fixed}</option>
            <option value="2"{if $ps_bonus.D.disctype == 2} selected{/if}>{$lng.lbl_ps_dtype_percent}</option>
          </select>
        </div>
        <div class="form-group">
        	<input type="text" class="form-control" name="ps_bonus[D][discount]" value="{$ps_bonus.D.discount}" id="ps-discount-value" size="5" maxlength="17" class='micro' />
		</div>

	    <ul class="ps-list">
	        <li class="radio"><label class="ps-chooser" for="ps-discount-cart"><input class="ps-chooser radio" type="radio" name="ps_bonus[D][apply]" id="ps-discount-cart" value="1"{if $ps_bonus.D.apply eq 1 || $ps_bonus.D.apply eq ''} checked="checked"{/if} /> {$lng.lbl_ps_disc_for_whole_cart}</label></li><br />
{*	        <li class="radio"><input class="ps-chooser" type="radio" name="ps_bonus[D][apply]" id="ps-discount-condition" value="2"{if $ps_bonus.D.apply eq 2} checked="checked"{/if} /><label class="ps-chooser" for="ps-discount-condition">{$lng.lbl_ps_disc_for_products_incondition}</label></li> *}
	        <li class="radio"><label class="ps-chooser" for="ps-discount-products"><input class="ps-chooser" type="radio" name="ps_bonus[D][apply]" id="ps-discount-products" value="3"{if $ps_bonus.D.apply eq 3} checked="checked"{/if} /> {$lng.lbl_ps_disc_for_selcted_products}</label></li>
	        <li class="ps-entities-block" id="ps-discount-products-block">
			<label for='ps_prods_id_prod_0' class='error' style='display:none'>Select at least one product or category</label>
            <table>
                <tr>
                    <th colspan="2">{$lng.lbl_ps_products}</th>
                </tr>
                {product_selector multiple=1 amount_name='quantity' prefix_name='ps_bonus[D][products]' prefix_id='ps_prods' products=$ps_bonus.D.products}
            </table>
            <table>
                <tr>
                    <th>{$lng.lbl_ps_categories}</th>
                </tr>
                
                <tr>{assign var='_group' value='ps_cats'}
                    <td id="{$_group}_box_1">
                        <div id="{$_group}_show_category" style="display:none">
                            <div class="bd"><div class="category_ajax" id="{$_group}_body">{$lng.lbl_loading}</div></div>
                        </div>
                        <input id="{$_group}_catid_0" type="text" class='micro' maxlength="11" name="ps_bonus[D][cats][0][id]" value="{$ps_bonus.D.cats[0].id}" />
                        <input id="{$_group}_catname_0" type="text" size="40" maxlength="255" name="ps_bonus[D][cats][0][name]" value="{$ps_bonus.D.cats[0].name|escape}" readonly="readonly" />
                        <input id="{$_group}_catqty_0" type="hidden" size="11" maxlength="11" name="ps_bonus[D][cats][0][quantity]" value="{$ps_bonus.D.cats[0].quantity|escape}" />
                        <img src="{$ImagesDir}/categories.png" onclick="ps_show_cats(this, '{$_group}');" id="{$_group}_link_0" />
                    </td>
                    <td id="{$_group}_add_button">{include file="main/multirow_add.tpl" mark=$_group is_lined=true}
                       <a href="javascript: void(0);" onclick="$(this).closest('tr').find('input[type=hidden],input[type=text]').val('');"><img src="{$ImagesDir}/admin/minus.png" /></a>
                    </td>
                </tr>
                
            </table>
    
    {if $ps_bonus.D.cats ne '' && $ps_bonus.D.cats|@count gt 1}
    {assign var='_group' value='ps_cats'}
    <script type="text/javascript">
        {foreach from=$ps_bonus.D.cats item=_elem name=$_group}
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
    </div>
    </td>
</tr>


<!-- FREE PRODUCTS -->

<tr id="ps-bonus-forfree">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_bonuses[F]" id='ps_bonuses_F' value="1"{if $ps_bonuses.F} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_bonus_forfree}{if $not_sav_bons.F}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-bonus-forfree-details">
		<table>
			<tr>
				<th colspan="2">{$lng.lbl_ps_products}</th>
			</tr>
			{product_selector multiple=1 amount_name='quantity' prefix_name='ps_bonus[F][products]' prefix_id='ps_prods2' products=$ps_bonus.F.products}
		</table>

    {if $ps_bonus.F.cats ne '' && $ps_bonus.F.cats|@count gt 1}
    {assign var='_group' value='ps_cats2'}
    <script type="text/javascript">
        {foreach from=$ps_bonus.F.cats item=_elem name=$_group}
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

<tr id="ps-bonus-freeship">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_bonuses[S]" id="ps_bonuses_S" value="1"{if $ps_bonuses.S} checked{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_bonus_freeship}{if $not_sav_bons.S}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-bonus-freeship-details">
      <div class="form-inline push-10">
        <label>Fixed rate: </label>
        <div class="form-group"><input type='text' class="form-control" name='ps_bonus[S][discount]'  placeholder='Fixed rate' value="{$ps_bonus.S.discount|default:0}" size='7'/></div>
        <div class="form-group">{$config.General.currency_symbol}</div>
   	  </div>

        <div class="form-group">{include file='main/select/shipping.tpl' name="ps_bonus[S][methods][]" values=$ps_bonus.S.methods multiple=1}</div>
	    <ul class="ps-list">
	        <li class="ratio"><label class="ps-chooser" for="ps-freeship-cart"><input class="ps-chooser" type="radio" name="ps_bonus[S][apply]" id="ps-freeship-cart" value="1"{if $ps_bonus.S.apply eq 1 || $ps_bonus.S.apply eq ''} checked="checked"{/if} /> {$lng.lbl_ps_disc_for_whole_cart}</label></li>
	        <li class="ratio"><label class="ps-chooser" for="ps-freeship-condition"><input class="ps-chooser" type="radio" name="ps_bonus[S][apply]" id="ps-freeship-condition" value="2"{if $ps_bonus.S.apply eq 2} checked="checked"{/if} /> {$lng.lbl_ps_disc_for_products_incondition}</label></li>
	        <li class="ratio"><label class="ps-chooser" for="ps-freeship-products"><input class="ps-chooser" type="radio" name="ps_bonus[S][apply]" id="ps-freeship-products" value="3"{if $ps_bonus.S.apply eq 3} checked="checked"{/if} /> {$lng.lbl_ps_disc_for_selcted_products}</label></li>
	        <li class="ps-entities-block ratio" id="ps-freeship-products-block">
			<label for='ps_bonus_group_S' class='error' style='display:none'></label>
            <table>
                <tr>
                    <th colspan="2">{$lng.lbl_ps_products}</th>
                </tr>
                {product_selector multiple=1 amount_name='quantity' prefix_name='ps_bonus[S][products]' prefix_id='ps_prods3' products=$ps_bonus.S.products}
            </table>
            <table>
                <tr>
                    <th>{$lng.lbl_ps_categories}</th>
                </tr>
                
                <tr>{assign var='_group' value='ps_cats3'}
                    <td id="{$_group}_box_1">
                        <div id="{$_group}_show_category" style="display:none">
                            <div class="bd"><div class="category_ajax" id="{$_group}_body">{$lng.lbl_loading}</div></div>
                        </div>
                        <input id="{$_group}_catid_0" type="text" class='micro' maxlength="11" name="ps_bonus[S][cats][0][id]" value="{$ps_bonus.S.cats[0].id}" />
                        <input id="{$_group}_catname_0" type="text" size="40" maxlength="255" name="ps_bonus[S][cats][0][name]" value="{$ps_bonus.S.cats[0].name|escape}" readonly="readonly" />
                        <input id="{$_group}_catqty_0" type="text" class='micro' maxlength="11" name="ps_bonus[S][cats][0][quantity]" value="{$ps_bonus.S.cats[0].quantity|escape}" />
                        <img src="{$ImagesDir}/categories.png" onclick="ps_show_cats(this, '{$_group}');" id="{$_group}_link_0" />
                    </td>
                    <td id="{$_group}_add_button">{include file="main/multirow_add.tpl" mark=$_group is_lined=true}
                       <a href="javascript: void(0);" onclick="$(this).closest('tr').find('input[type=hidden],input[type=text]').val('');"><img src="{$ImagesDir}/admin/minus.png" /></a>
                    </td>
                </tr>
                
            </table>
    
    {if $ps_bonus.S.cats ne '' && $ps_bonus.S.cats|@count gt 1}
    {assign var='_group' value='ps_cats3'}
    <script type="text/javascript">
        {foreach from=$ps_bonus.S.cats item=_elem name=$_group}
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

<!-- Coupon -->
<tr id="ps-bonus-coupon">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_bonuses[C]" id="ps_bonuses_C" value="1"{if $ps_bonuses.C} checked{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_bonus_coupon}{if $not_sav_bons.C}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-bonus-coupon-details">
    <div id="ps-coupons-list">
    {include file="addons/promotion_suite/admin/coupons.tpl" ps_type="C"}
    </div>
    <div style="padding: 10px 0;">
       <input type="button" class="btn-green btn push-5-r" title="{$lng.lbl_ps_refresh_list}" value="{$lng.lbl_ps_refresh_list}" id="ps-refresh-list" />
	<input type="button" class="btn-green btn push-5-r" title="{$lng.lbl_ps_new_coupon_title}" value="{$lng.lbl_ps_new_coupon}" onclick="window.open(ps_url_add_coupon);" />
    </div>
    </div></td>
</tr>
</table>

</div>
