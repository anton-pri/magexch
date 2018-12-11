<div class="box">
{include file='common/subheader.tpl' title=$lng.lbl_ps_conditions}

<script type="text/javascript">
//<!--
var ps_zones = new Object();
var ps_url_get_zones = "{$ps_url_get_zones|escape:javascript}";
var ps_url_add_zone = "{$ps_url_add_zone|escape:javascript}";

{if $ps_bonus.B.coupon ne ''}
var cond_coupon_code = "{$ps_bonus.B.coupon}";
{else}
var cond_coupon_code = null;
{/if}

//-->
</script>


<table class="ps-bonuses" width='100%'>
<!-- Total -->
<tr id="ps-cond-subtotal">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[T]" id='ps_conditions_T' value="1"{if $ps_conditions.T} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_cond_subtotal}{if $not_sav_conds.T}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details form-inline" id="ps-cond-subtotal-details">
        <label>{$lng.lbl_ps_disc_subtotal_interval}&nbsp;
            <div class="form-group"><input type="text" class="form-control" name="ps_conds[T][from]" value="{$ps_conds.T.from|default:0}" id="ps-subtotal-from-value" class='micro' maxlength="17" /></div>
        </label>
        <label>{$lng.lbl_to|lower}&nbsp;
            <div class="form-group"><input type="text"  class="form-control" name="ps_conds[T][till]" value="{$ps_conds.T.till|default:0}" id="ps-subtotal-to-value" class='micro' maxlength="17" /></div>
            {$config.General.currency_symbol}
        </label>
    </div></td>
</tr>

<!-- Address -->
<tr id="ps-cond-shipping">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[A]" value="1"{if $ps_conditions.A} checked{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_cond_shipping}{if $not_sav_conds.C}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-cond-shipping-details">
    <div id="ps-zones-list">
    {include file="addons/promotion_suite/admin/zones.tpl"}
    </div>
    <input type="button" class="btn btn-green" title="{$lng.lbl_ps_refresh_list}" value="{$lng.lbl_ps_refresh_list}" id="ps-refresh-zones-list" />
	<input type="button" class="btn btn-green" title="{$lng.lbl_ps_new_zone_title}" value="{$lng.lbl_ps_new_zone}" onclick="window.open(ps_url_add_zone);" />
    </div></td>
</tr>

<!-- Product -->
<tr id="ps-cond-products">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[P]" value="1"{if $ps_conditions.P} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_cond_products}{if $not_sav_conds.P}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-cond-products-details">
		<label for='ps_prods4_id_prod_0' class='error' style='display:none'></label>
        <!-- Certain product -->
        <table width="100%" class="push-20">
            <tr>
                <th colspan="2" align='left'><h3 class="block-title push-5">{$lng.lbl_ps_products}</h3></th>
            </tr>
			{product_selector multiple=1 amount_name='quantity' prefix_name='ps_conds[P][products]' prefix_id='ps_prods4' products=$ps_conds.P.products}
        </table>
        <!-- Product from category -->
        <table width="100%">
            <tr>
                <th align='left'><h3 class="block-title push-5">{$lng.lbl_ps_categories}</h3></th>
            </tr>
            
            <tr>{assign var='_group' value='ps_cats4'}
                <td id="{$_group}_box_1" class="form-inline">
                <div class="push-20">
                    <div id="{$_group}_show_category" style="display:none">
                        <div class="bd"><div class="category_ajax" id="{$_group}_body">{$lng.lbl_loading}</div></div>
                    </div>
                    <div class="form-group"><input class="form-control" id="{$_group}_catid_0" type="text" class='micro' maxlength="11" name="ps_conds[P][cats][0][id]" value="{$ps_conds.P.cats[0].id}" /></div>
                    <div class="form-group"><input class="form-control" id="{$_group}_catname_0" type="text" size="40" maxlength="255" name="ps_conds[P][cats][0][name]" value="{$ps_conds.P.cats[0].name|escape}" readonly="readonly" /></div>
                    <div class="form-group"><input class="form-control" id="{$_group}_catqty_0" type="text" class='micro' maxlength="11" name="ps_conds[P][cats][0][quantity]" value="{$ps_conds.P.cats[0].quantity|escape}" /></div>
                    <div class="form-group"><img src="{$ImagesDir}/categories.png" style="padding: 6px 4px;" onclick="ps_show_cats(this, '{$_group}');" id="{$_group}_link_0" /></div>
				</div>
                </td>
            </tr>
        </table>
        <!-- Product of manufacturer -->
        {if $ps_mans && $ps_mans|@count gt 0}
        <table class="push-20">
            <tr>
                <th colspan="2" align='left'><h3 class="block-title push-5">{$lng.lbl_ps_manufacturers}</h3></th>
            </tr>
            <tr>
                <td id="ps_mans_box_1" class="form-inline">
                <div class="form-group">
                    <select id="ps_mans_id_0" name="ps_conds[P][mans][0][id]" size="1" class="form-control">
                    	<option value="0">{$lng.lbl_ps_select_element}</option>
                    	{foreach from=$ps_mans item='manufacturer'}
                        <option value="{$manufacturer.manufacturer_id}"{if $ps_conds.P.mans[0].id == $manufacturer.manufacturer_id} selected{/if}>{$manufacturer.manufacturer}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <input id="ps_mans_qty_0" type="text" class='form-control' maxlength="11" name="ps_conds[P][mans][0][quantity]" value="{$ps_conds.P.mans[0].quantity|escape}" />
                </div>
                </td>
                <td id="ps_mans_add_button">{include file="main/multirow_add.tpl" mark="ps_mans" is_lined=true}
                <a href="javascript: void(0);" onclick="$(this).closest('tr').find('select,input[type=hidden],input[type=text]').val('');"><img src="{$ImagesDir}/admin/minus.png" /></a>
                </td>
            </tr>
        </table>
        {/if}
        <!-- Products with attributes -->
        <table>
            <tr>
                <th align='left'><h3 class="block-title push-5">{$lng.lbl_attributes}</h3></th>
            </tr>
            <tr>
            <td>
				<div id='ps_attributes' class="form-inline">
				<div class="form-group">
				  <select class="form-control" id='new_attribute_cond' size="1">
					{foreach from=$ps_attr item='attributes'}
					<option value="{$attributes.attribute_id}">{$attributes.name}</option>
					{/foreach}
				  </select>
				</div>
				<div class="form-group">
				  <a onclick="javascript: ajaxGet('index.php?target=promosuite&action=attributes&attribute_id='+$('#new_attribute_cond').val());" href="javascript: void(0);">
				    <img src="{$ImagesDir}/admin/plus.png" align='top' alt='+' />
				  </a>
				  				<sup>* {$lng.txt_ps_alphabetical_order}</sup>

				</div>
				<script type="text/javascript">
					$i = 0;
					{foreach from=$ps_conds.P.attr item=_elem}
					 setTimeout("ajaxGet('index.php?target=promosuite&action=attributes&cd_id={$_elem.cd_id}')",100*$i);$i++;
					{/foreach}
				</script>
				</div>
			</td>
			</tr>
        </table>

    {if $ps_conds.P.cats ne '' && $ps_conds.P.cats|@count gt 1}
    {assign var='_group' value='ps_cats4'}
    <script type="text/javascript">
        {foreach from=$ps_conds.P.cats item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_catid/, value: '{$_elem.id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catqty/, value: '{$_elem.quantity|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_link/, value: '0'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
    
    {if $ps_mans ne '' && $ps_conds.P.mans ne '' && $ps_conds.P.mans|@count gt 1}
    {assign var='_group' value='ps_mans'}
    <script type="text/javascript">
        {foreach from=$ps_conds.P.mans item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_id/, value: '{$_elem.id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_qty/, value: '{$_elem.quantity|escape}'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
    
    </div></td>
</tr>

<!-- Weight -->
<tr id="ps-cond-weight">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[W]" value="1"{if $ps_conditions.W} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_ps_cond_weight}{if $not_sav_conds.W}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details form-inline" id="ps-cond-weight-details">
        <label>{$lng.lbl_ps_weight_interval}&nbsp;
            <div class="form-group"><input type="text" class="form-control" name="ps_conds[W][from]" value="{$ps_conds.W.from|default:0}" title="{$lng.lbl_from}" id="ps-weight-from-value" class='micro' maxlength="6" /></div>
        </label>
        <label>{$lng.lbl_to|lower}&nbsp;
            <div class="form-group"><input type="text" class="form-control" name="ps_conds[W][till]" value="{$ps_conds.W.till|default:0}" title="{$lng.lbl_to}" id="ps-weight-to-value" class='micro' maxlength="6" /></div>
            {$config.General.weight_symbol}
        </label>
    </div></td>
</tr>

<!-- Membership -->
<tr id="ps-cond-membership">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[E]" value="1"{if $ps_conditions.E} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_membership}{if $not_sav_conds.E}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-cond-membership-details">
        <label><span>{$lng.lbl_user_has_membership}&nbsp;</span>
        </label>
        {include file="admin/select/membership.tpl" name="ps_conds[E][membership]" id="ps-membership-value" is_please_select=1 value=$ps_conds.E.membership memberships=$ps_memberships}

    </div></td>
</tr>

<!-- Coupon -->
<tr id="ps-cond-apply_coupon">
    <td rowspan="2" class="ps-checkbox"><input type="checkbox" name="ps_conditions[B]" value="1"{if $ps_conditions.B} checked="checked"{/if} /></td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">{$lng.lbl_used_coupon}{if $not_sav_conds.B}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a></td>
</tr>
<tr>
    <td><div class="ps-bonus-details" id="ps-cond-apply_coupon-details">
        <label>{$lng.lbl_user_used_specific_coupon} </label>
        <div id="ps-cond-coupons-list">
            {include file="addons/promotion_suite/admin/coupons.tpl" ps_type="B"}
        </div>
        <div style="padding: 10px 0;">
        <input type="button" class="btn btn-green push-5-r" title="{$lng.lbl_ps_refresh_list}" value="{$lng.lbl_ps_refresh_list}" id="ps-cond-refresh-coupons-list" />
        <input type="button" class="btn btn-green push-5-r" title="{$lng.lbl_ps_new_coupon_title}" value="{$lng.lbl_ps_new_coupon}" onclick="window.open(ps_url_add_coupon);" />
        </div>
    </div></td>
</tr>

<!-- Cookies -->
<tr id="ps-cond-apply_cookies">
    <td rowspan="2" class="ps-checkbox">
        <input type="checkbox" name="ps_conditions[K]" value="1"{if $ps_conditions.K} checked="checked"{/if} />
    </td>
    <td class="ps-bonus-body"><a class="ps-bonus-picker" href="#" onclick="return false;">
        Cookies{if $not_sav_conds.K}&nbsp;&nbsp;<span class="ps-not-saved">{$lng.lbl_ps_not_saved}</span>{/if}</a>
    </td>
</tr>
<tr>
    <td>
    <div class="ps-bonus-details" id="ps-cond-apply_cookies-details">
        <div class="form-group form-inline">
        <label>Cookie 
        <input type="text"  class="form-control" name="ps_conds[K][cookie]" value="{$ps_conds.K.cookie}" />
        </label>
        
        <select name="ps_conds[K][operation]">
        <option value='E' {if $ps_conds.K.operation eq 'E'}selected='selected'{/if}>Exists</option>
        <option value='N' {if $ps_conds.K.operation eq 'N'}selected='selected'{/if}>Not exists</option>
        <option value='S' {if $ps_conds.K.operation eq 'S'}selected='selected'{/if}>Is set to:</option>
        </select>
        
        <label>Value
        <input type="text"  class="form-control" name="ps_conds[K][value]" value="{$ps_conds.K.value}" />
        </label>
        </div>
        <div class="form-group form-inline">
            <label>Action after order placement</label>
            <select name="ps_conds[K][postaction]">
            <option value='' {if $ps_conds.K.postaction eq ''}selected='selected'{/if}>No action</option>
            <option value='U' {if $ps_conds.K.postaction eq 'U'}selected='selected'{/if}>Unset cookie</option>
            <option value='S' {if $ps_conds.K.postaction eq 'S'}selected='selected'{/if}>Set to:</option>
            </select>
            <input type="text"  class="form-control" name="ps_conds[K][postvalue]" value="{$ps_conds.K.postvalue}" class='micro' />
        </div>
    </div>
    </td>
</tr>
</table>

</div>
