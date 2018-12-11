<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_arrivals}
    	<input type="checkbox" name="ins_sections[arrivals][insert_to_section]" value="Y" class="separate_checkbox" {if $sec.arrivals.id}checked{/if}>
    </label>
    <div class="col-xs-12 form-inline">
      <div class="form-group">{include file='main/select/date.tpl' name='ins_sections[arrivals][from_time]' value=$sec.arrivals.from_time}</div> 
      <div class="form-group"> - </div>
      <div class="form-group">{include file='main/select/date.tpl' name='ins_sections[arrivals][to_time]' value=$sec.arrivals.to_time}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_min_stc_lev}</label>
    <div class="col-xs-12 col-sm-3 col-md-2"><input class="input50 form-control" type="text" name="ins_sections[arrivals][min_amount]" value="{$sec.arrivals.min_amount}" size="4"></div>
    <input type="hidden" name="ins_sections[arrivals][active]" value="Y">
    <input type="hidden" name="ins_sections[arrivals][side_box]" value="Y">
</div>
<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_hot_deals}
         <input type="checkbox" name="ins_sections[hot_deals][insert_to_section]" value="Y" class="separate_checkbox" {if $sec.hot_deals.id}checked{/if}>
    </label>
    <div class="col-xs-12 form-inline">
    	<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[hot_deals][from_time]' value=$sec.hot_deals.from_time}</div>
    	<div class="form-group"> - </div>
    	<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[hot_deals][to_time]' value=$sec.hot_deals.to_time}</div>
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_min_stc_lev}</label>
    <div class="col-xs-12 col-sm-3 col-md-2"><input class="input50 form-control" type="text" name="ins_sections[hot_deals][min_amount]" value="{$sec.hot_deals.min_amount}" size="4"></div>

    <input type="hidden" name="ins_sections[hot_deals][active]" value="Y">
    <input type="hidden" name="ins_sections[hot_deals][home_page]" value="Y">
</div>
<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_clearance}
    	<input type="checkbox" name="ins_sections[clearance][insert_to_section]" value="Y" class="separate_checkbox" {if $sec.clearance.id}checked{/if}>
    </label>
    <div class="col-xs-12 form-inline">
   		<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[clearance][from_time]' value=$sec.clearance.from_time}</div>
   		<div class="form-group"> - </div>
    	<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[clearance][to_time]' value=$sec.clearance.to_time}</div>
    </div>
</div>
<div class="form-group">
    <label>{$lng.lbl_min_stc_lev}</label>
    <div class="col-xs-12 col-sm-3 col-md-2"><input class="input50 form-control" type="text" name="ins_sections[clearance][min_amount]" value="{$sec.clearance.min_amount}" size="4"></div>
    <input type="hidden" name="ins_sections[clearance][active]" value="Y">
    <input type="hidden" name="ins_sections[clearance][home_page]" value="Y">
</div>
<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_super_deals}
        <input type="checkbox" name="ins_sections[super_deals][insert_to_section]" value="Y" class="separate_checkbox" {if $sec.super_deals.id}checked{/if}>
    </label>
    <div class="col-xs-12 form-inline">
		<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[super_deals][from_time]' value=$sec.super_deals.from_time}</div>
		<div class="form-group"> - </div>
    	<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[super_deals][to_time]' value=$sec.super_deals.to_time}</div>
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_min_stc_lev}</label>
    <div class="col-xs-12 col-sm-3 col-md-2"><input class="input50 form-control" type="text" name="ins_sections[super_deals][min_amount]" value="{$sec.super_deals.min_amount}" size="4"></div>
    <input type="hidden" name="ins_sections[super_deals][active]" value="Y">
</div>
<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_featured_products}
    	<input type="checkbox" name="ins_sections[featured_products][insert_to_section]" value="Y" class="separate_checkbox" {if $sec.featured_products.product_id}checked{/if}>
    </label>
    <div class="col-xs-12 form-inline">
        <div class="form-group">{include file='main/select/date.tpl' name='ins_sections[featured_products][from_time]' value=$sec.featured_products.from_time}</div>
        <div class="form-group"> - </div>
    	<div class="form-group">{include file='main/select/date.tpl' name='ins_sections[featured_products][to_time]' value=$sec.featured_products.to_time}</div>
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_min_stc_lev}</label>
    <div class="col-xs-12 col-sm-3 col-md-2"><input class="input50 form-control" type="text" name="ins_sections[featured_products][min_amount]" value="{$sec.featured_products.min_amount}" size="4"></div>

    {if $sec.featured_products.product_id}
    <div class="form-group" style="padding-left:0;">
    <label>{$lng.lbl_avail_type_avail}:</label>
      <div class="width570px" style="float:left">
        <input type="checkbox" name="ins_sections[featured_products][avail]" value="1" {if $sec.featured_products.avail eq 1}checked{/if}>
        {$lng.featured_prod_warning}
      </div>
    </div>
    {else}
    <input type="hidden" name="ins_sections[featured_products][avail]" value="1">
    {/if}
    <input type="hidden" name="ins_sections[featured_products][type]" value="0">
</div>
