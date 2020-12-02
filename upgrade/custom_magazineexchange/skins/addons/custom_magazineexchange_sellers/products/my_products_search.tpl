  <div class="form-group">
    <label class="col-xs-12">
      <input type="checkbox" name="posted_data[only_this_seller]"{if $search_prefilled eq "" or $search_prefilled.only_this_seller} checked="checked"{/if} value="1" />&nbsp;{$lng.lbl_only_my_products|default:'Only My Products'}
    </label>
  </div>