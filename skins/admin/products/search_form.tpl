{if $included_tab eq 'basic_search'}
{* start *}
<div class="form-horizontal">

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_search_for_pattern}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="posted_data[substring]" value="{$search_prefilled.substring|escape}" class="form-control" /></div>
</div>
  <!-- cw@search_in_params [ -->
  <div class="form-group">
      <label class="col-xs-12">{$lng.lbl_search_in}</label>
        <div class="col-xs-12">
            <label class="checkbox-inline"><input type="checkbox" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} value="1" />
            {$lng.lbl_product_title}&nbsp;</label>
            <label class="checkbox-inline"><input type="checkbox" name="posted_data[by_shortdescr]"{if $search_prefilled eq "" or $search_prefilled.by_shortdescr} checked="checked"{/if} value="1" />
            {$lng.lbl_short_description}&nbsp;</label>
            <label class="checkbox-inline"><input type="checkbox" name="posted_data[by_fulldescr]"{if $search_prefilled eq "" or $search_prefilled.by_fulldescr} checked="checked"{/if} value="1" />
            {$lng.lbl_det_description}&nbsp;</label>
        </div>
    {if $config.search.allow_search_by_words eq 'Y'}
      <div class="col-xs-12">
          <label class="radio-inline"><input type="radio" name="posted_data[including]" value="all"{if $search_prefilled eq "" or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} />
          {$lng.lbl_all_word}&nbsp;</label>
          <label class="radio-inline"><input type="radio" name="posted_data[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} />
          {$lng.lbl_any_word}&nbsp;</label>
          <label class="radio-inline"><input type="radio" name="posted_data[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} />
          {$lng.lbl_exact_phrase}&nbsp;</label>
      </div>
    {/if}
  </div>
  <!-- cw@search_in_params ] -->
</div>

{elseif $included_tab eq 'add_search'}
{* start *}
<div class="form-horizontal">

{if $config.Appearance.categories_in_products eq '1'}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_category}</label>
    <div class="col-xs-12">{include file='admin/select/category.tpl' name='posted_data[categories][]' value=$search_prefilled.categories_orig multiple=1}</div>
</div>
<div class="form-group">
  <div class="col-xs-12">
    <div class="checkbox">
      <label>
        <input type="checkbox" name="posted_data[search_in_subcategories]"{if $search_prefilled eq "" or $search_prefilled.search_in_subcategories} checked="checked"{/if} value="1" />
        {$lng.lbl_search_in_subcategories}:&nbsp;
      </label>
    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" name="posted_data[category_main]"{if $search_prefilled eq "" or $search_prefilled.category_main} checked="checked"{/if} value="1" />
        {$lng.lbl_main_category}&nbsp;
      </label>
    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" name="posted_data[category_extra]"{if $search_prefilled.category_extra} checked="checked"{/if} value="1" />
        {$lng.lbl_additional_category}
      </label>
    </div>
  </div>
</div>
{/if}

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_sku}:</label>
    <div class="col-xs-12 col-md-4 col-lg-2"><input type="text" name="posted_data[productcode]" value="{$search_prefilled.productcode}" class="form-control" /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_manufacturer_code}:</label>
    <div class="col-xs-12 col-md-4 col-lg-2"><input type="text" name="posted_data[manufacturer_code]" value="{$search_prefilled.manufacturer_code}" class="form-control" /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_eancode}:</label>
    <div class="col-xs-12 col-md-4 col-lg-2"><input type="text" name="posted_data[eancode]" value="{$search_prefilled.eancode}" class="form-control" /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_id}#:</label>
    <div class="col-xs-12 col-md-4 col-lg-2"><input type="text" name="posted_data[product_id]" value="{$search_prefilled.product_id}" class="form-control" /></div>
</div>

{if $addons.sn}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_serial_number}:</label>
    <div class="col-xs-12 col-md-4 col-lg-2"><input type="text" name="posted_data[serial_number]" value="{$search_prefilled.serial_number}" class="form-control" /></div>
</div>
{/if}

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_availability}:</label>
    <div class="col-xs-12 col-md-4">{include file='admin/select/availability_product.tpl' name="posted_data[status][]" value=$search_prefilled.status[0]}</div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_avail}:</label>
    <div class="col-xs-12 col-md-4">{include file='admin/select/product_avail.tpl' name='posted_data[avail_types][]' selected=$search_prefilled.avail_types multiple=1}</div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_search_tags}:</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="posted_data[tag]" value="{$search_prefilled.tag}" class="form-control" /></div>
</div>

</div>

{elseif $included_tab eq 'prices'}
{* start *}
<div class="form-horizontal">

  <div class="form-group">
    <label class="col-xs-12">{$lng.lbl_selling_price} ({$config.General.currency_symbol}):</label>
    <div class="col-xs-12 form-inline">
        <div class="form-group"><input type="text" size="10" maxlength="15" class="form-control" name="posted_data[price_min]" value="{$search_prefilled.price_min|formatprice}" /></div>
        <div class="form-group">&nbsp;-&nbsp;</div>
        <div class="form-group"><input type="text" size="10" maxlength="15" class="form-control" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></div>
    </div>
  </div>
  <div class="form-group">
     <label class="col-xs-12">{$lng.lbl_list_price} ({$config.General.currency_symbol}):</label>
     <div class="col-xs-12 form-inline">
        <div class="form-group"><input type="text" size="10" maxlength="15" class="form-control" name="posted_data[list_price_min]" value="{$search_prefilled.list_price_min|formatprice}" /></div>
        <div class="form-group">&nbsp;-&nbsp;</div>
        <div class="form-group"><input type="text" size="10" maxlength="15" class="form-control" name="posted_data[list_price_max]" value="{$search_prefilled.list_price_max|formatprice}" /></div>
     </div>
  </div>
</div>
{elseif $included_tab eq 'additional_options'}
{* start *}
<div class="form-horizontal">

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_free_shipping}</label>
    <div class="col-xs-12 col-md-4">
      <select name="posted_data[flag_free_ship]" class="form-control">
        <option value="">{$lng.lbl_any}</option>
        <option value="Y"{if $search_prefilled.flag_free_ship eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_free_ship eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_shipping_freight}</label>
    <div class="col-xs-12 col-md-4">
      <select name="posted_data[flag_ship_freight]" class="form-control">
        <option value="">{$lng.lbl_any}</option>
        <option value="Y"{if $search_prefilled.flag_ship_freight eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_ship_freight eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_global_discounts}</label>
    <div class="col-xs-12 col-md-4">
      {include file='admin/select/yes_no.tpl' name='posted_data[flag_global_disc]' value=$search_prefilled.flag_global_disc}
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_tax_exempt}</label>
    <div class="col-xs-12 col-md-4">
      <select name="posted_data[flag_free_tax]" class="form-control">
        <option value="">{$lng.lbl_any}</option>
        <option value="Y"{if $search_prefilled.flag_free_tax eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_free_tax eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_min_order_amount}:</label>
    <div class="col-xs-12 col-md-4">
      <select name="posted_data[flag_min_amount]" class="form-control">
        <option value="">{$lng.lbl_any}</option>
        <option value="Y"{if $search_prefilled.flag_min_amount eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_min_amount eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_lowlimit_in_stock}:</label>
    <div class="col-xs-12 col-md-4">
      <select name="posted_data[flag_low_avail_limit]" class="form-control">
        <option value="">{$lng.lbl_any}</option>
        <option value="Y"{if $search_prefilled.flag_low_avail_limit eq "Y"} selected="selected"{/if}>{$lng.lbl_assigned}</option>
        <option value="N"{if $search_prefilled.flag_low_avail_limit eq "N"} selected="selected"{/if}>{$lng.lbl_not_assigned}</option>
      </select>
    </div>
</div>

<div class="form-group">
  <div class="col-xs-12">
    <div class="checkbox">
      <label>
      <input type="checkbox" name="posted_data[blank_descr]"{if $search_prefilled.blank_descr} checked="checked"{/if} value=1 />
      {$lng.lbl_items_with_blank_descr}
      </label>
    </div>
  </div>

  <div class="col-xs-12">
    <div class="checkbox">
      <label>
      <input type="checkbox" name="posted_data[code]"{if $search_prefilled.code} checked="checked"{/if} value=1 />
      {$lng.lbl_items_with_code}
      </label>
    </div>
  </div>

  <div class="col-xs-12">
    <div class="checkbox">
      <label>
      <input type="checkbox" name="posted_data[without_code]"{if $search_prefilled.without_code} checked="checked"{/if} value=1 />
      {$lng.lbl_items_without_code}
      </label>
    </div>
  </div>
</div>


<div class="form-group">
  <label class="col-xs-12">{$lng.lbl_product_image}</label>
  <div class="col-xs-12">
        <select name="posted_data[has_image]">
        <option value=''></option>
        <optgroup label='Product has image'>
            <option value='UL' {if $search_prefilled.has_image eq 'UL'} selected='selected'{/if}>Uploaded or external URL</option>
            <option value='U' {if $search_prefilled.has_image eq 'U'} selected='selected'{/if}>Uploaded only</option>
            <option value='L' {if $search_prefilled.has_image eq 'L'} selected='selected'{/if}>External URL link only</option>
        </optgroup>
        <optgroup label='Without image'>
            <option value='N' {if $search_prefilled.has_image eq 'N'} selected='selected'{/if}>Without any image</option>
            <option value='NL' {if $search_prefilled.has_image eq 'NL'} selected='selected'{/if}>Without uploaded image</option>
        </optgroup>
        
        </select>
  </div>
</div>
  
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_sold_between}</label>
    <div class="input-daterange input-group">
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[sold_date_start]" value=$search_prefilled.sold_date_start}</div>
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[sold_date_end]" value=$search_prefilled.sold_date_end}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_created_between}</label>
    <div class="input-daterange input-group">
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[creation_date_start]" value=$search_prefilled.creation_date_start}</div>
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[creation_date_end]" value=$search_prefilled.creation_date_end}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_modified_between}</label>
    <div class="input-daterange input-group">
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[modify_date_start]" value=$search_prefilled.modify_date_start}</div>
      <div class="col-md-2 col-xs-6">{include file="main/select/date.tpl" name="posted_data[modify_date_end]" value=$search_prefilled.modify_date_end}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_created_by}</label>
    <div class="col-xs-12 col-md-4">{include file='admin/select/find_user.tpl' name='posted_data[created_by]' value=$search_prefilled.created_by form='search_form' area='A'}</div>
</div>

</div>

{/if}
