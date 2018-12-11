<div class="form-group">
<label class="col-xs-12">{$lng.lbl_sms_to_customer|default:'SMS to customer'} <input type="checkbox" name="posted_data[{$os.code}][sms_customer]" {if $os.sms_customer}checked="checked"{/if} value="1"/>
</label>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_sms_message|default:'SMS message'}</label>
  <div class="col-xs-12">
	<textarea class="form-control" name="posted_data[{$os.code}][sms_message]" style="width: auto" cols="65" rows="2">{$os.sms_message}</textarea>
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_sms_to_seller|default:'SMS to seller'} <input type="checkbox" name="posted_data[{$os.code}][sms_seller]" {if $os.sms_seller}checked="checked"{/if} value="1"/>
</label>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_sms_seller_message|default:'SMS message to seller'}</label>
  <div class="col-xs-12">
        <textarea class="form-control" name="posted_data[{$os.code}][sms_seller_message]" style="width: auto" cols="65" rows="2">{$os.sms_seller_message}</textarea>
  </div>
</div>

