<div class="box form-horizontal">

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_payment_code}</label>
    <div class="col-xs-12"><input type="text" class="form-control" name="posted_data[payment_code]" value="{$payment.payment_code|escape}" /></div>
</div>
<div class="form-group">
    <label class='multilan col-xs-12'>
        {$lng.lbl_title}
        
    </label>
    <div class="col-xs-12"><input type="text" class="form-control" name="posted_data[title]" value="{$payment.title|escape}" /></div>
</div>
<div class="form-group">
    <label class='multilan col-xs-12'>
        {$lng.lbl_descr}
        
    </label>
    <div class="col-xs-12"><textarea  class="form-control" name="posted_data[descr]" cols="75" rows="6">{$payment.descr}</textarea></div>
</div>
<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_active}
    </label>
    <div class="col-xs-12 col-sm-6 col-md-3">{include file='admin/select/yes_no.tpl' name='posted_data[active]' value=$payment.active}</div>
</div>
<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_orderby}</label>
    <div class="col-xs-12 col-sm-6 col-md-3"><input type="text" class="form-control" size="5" maxlength="5" name="posted_data[orderby]" value="{$payment.orderby}" /></div>
</div>

<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_payment_operations}</label>
    <div class="col-xs-12">{include file='admin/select/payment_operations.tpl' name="posted_data[payment_operations][]" value=$payment.payment_operations}</div>
</div>
<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_membership}</label>
    <div class="col-xs-12">{include file='admin/select/membership.tpl' name="posted_data[membership_ids][]" value=$payment.memberships multiple=1}</div>
</div>
<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_shipping}</label>
    <div class="col-xs-12">{include file='admin/select/shipping.tpl' name="posted_data[shippings_ids][]" values=$payment.shippings multiple=1}</div>
</div>

<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_min_plimit} - {$lng.lbl_max_plimit}</label>
    <div class="col-xs-12 form-inline">
    	<div class="form-group"><input type="text" class="form-control" size="5" name="posted_data[min_limit]" value="{$payment.min_limit}" style="width: 100px;" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" size="5" name="posted_data[max_limit]" value="{$payment.max_limit}" style="width: 100px;" /></div>
    </div>
</div>

<div class="form-group">
    <label class=" col-xs-12">{$lng.lbl_payment_surcharge}</label>
    <div class="col-xs-12 form-inline">
    	<div class="form-group"><input type="text" class="form-control" size="8" name="posted_data[surcharge]" value="{$payment.surcharge}" style="float:left; margin-top: 2px; margin-right: 10px;width: 100px;" /></div>
    	<div class="form-group">{include file='admin/select/modifier_type.tpl' name="posted_data[surcharge_type]" value=$payment.surcharge_type}</div>
	</div>
</div>


{include file='admin/attributes/object_modify.tpl'}
</div>

