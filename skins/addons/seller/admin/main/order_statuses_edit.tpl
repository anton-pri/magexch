<div class="form-group">
<label class="col-xs-12">{$lng.lbl_seller_is_notified|default:'Email to seller'} <input type="checkbox" name="posted_data[{$os.code}][email_seller]" {if $os.email_seller}checked="checked"{/if} value="1"/>
</label>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_seller_email_subject|default:'Seller email subject'}</label>
  <div class="col-xs-12">
        <input type="text" class="form-control" name="posted_data[{$os.code}][email_subject_seller]" value="{$os.email_subject_seller|escape}" size="65" />
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_seller_email_message|default:'Seller email'}</label>
  <div class="col-xs-12">
     <textarea name="posted_data[{$os.code}][email_message_seller]" class="form-control" style="width: auto" cols="65" rows="7">{$os.email_message_seller}</textarea>
  </div>
</div>
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_seller_email_message_modify_mode|default:'Seller email body:'}</label>
  <div class="col-xs-12">
        <select class="form-control" name="posted_data[{$os.code}][email_message_seller_mode]">
                <option value="I" {if $os.email_message_seller_mode eq "" or $os.email_message_seller_mode eq "I"}selected="selected"{/if}>{$lng.lbl_inserted_into_default_template|default:'inserted into default template'}</option>
                <option value="R" {if $os.email_message_seller_mode eq "R"}selected="selected"{/if}>{$lng.lbl_replaces_default_template|default:'replaces default template'}</option>
        </select>
  </div>
</div>
<div class="form-group">
<div class="col-xs-12"><a href="javascript: popup_preview_order_emails('{$os.code}', 'seller'); void(0);" class="btn btn-minw btn-info">{$lng.lbl_os_seller_emails_preview|default:'Seller emails preview'}</a></div>
</div>
