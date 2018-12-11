{*include file='common/page_title.tpl' title=$lng.lbl_ebay_export*}
{capture name=section}
{capture name=block}

{$lng.txt_ebay_export_note|default:'ebay Export'}

<form method="post" name="ebay_export_form" id="ebay_export_form" class="form-horizontal">
<input type="hidden" name="mode" value="export" />

<div class="box">

{include file="common/subheader.tpl" title=$lng.lbl_export_options}

<div class="form-horizontal">
<table class="table table-striped dataTable vertical-center">

<tr>
    <td>{$lng.lbl_new_filename}</td>
    <td><input name='file_name' class="form-control" type='text' value="{$ebay_file_name}" /></td>
</tr>

<tr>
    <td>Action</td>
    <td>
        <select name='ebay_action' class="form-control">
            <option value='Add' {if $config.ebay.ebay_action eq 'Add'}selected='selected'{/if}>Add</option>
            <option value='VerifyAdd' {if $config.ebay.ebay_action eq 'VerifyAdd'}selected='selected'{/if}>Verify Add</option>
            <option value='Revise' {if $config.ebay.ebay_action eq 'Revise'}selected='selected'{/if}>Revise</option>
        </select>
    </td>
</tr>

<tr>
    <td>Default category</td>
    <td>
        <select name='ebay_category' class="form-control">
            {foreach from=$ebay_categories item=category}
                <option value="{$category.id}"{if $config.ebay.ebay_category eq $category.id} selected="selected"{/if}>{$category.name}</option>
            {/foreach}
        </select>
    </td>
</tr>
<tr>
    <td>ConditionID</td>
    <td>
        <select name='ebay_condition_id' class="form-control">
            <option value="1000"{if $config.ebay.ebay_condition_id eq 1000} selected="selected"{/if}>New</option>
            <option value="1500"{if $config.ebay.ebay_condition_id eq 1500} selected="selected"{/if}>New other (see details)</option>
            <option value="1750"{if $config.ebay.ebay_condition_id eq 1750} selected="selected"{/if}>New with defects</option>
            <option value="2000"{if $config.ebay.ebay_condition_id eq 2000} selected="selected"{/if}>Manufacturer refurbished</option>
            <option value="2500"{if $config.ebay.ebay_condition_id eq 2500} selected="selected"{/if}>Seller refurbished</option>
            <option value="3000"{if $config.ebay.ebay_condition_id eq 3000} selected="selected"{/if}>Used</option>
            <option value="4000"{if $config.ebay.ebay_condition_id eq 4000} selected="selected"{/if}>Very Good</option>
            <option value="5000"{if $config.ebay.ebay_condition_id eq 5000} selected="selected"{/if}>Good</option>
            <option value="6000"{if $config.ebay.ebay_condition_id eq 6000} selected="selected"{/if}>Acceptable</option>
            <option value="7000"{if $config.ebay.ebay_condition_id eq 7000} selected="selected"{/if}>For parts or not working</option>
        </select>
    </td>
</tr>



<tr>
    <td>How long would you like your listing to be posted on eBay? (days)</td>
    <td>
        <input name='ebay_duration' class="form-control" type='text' value='{$config.ebay.ebay_duration}' />
    </td>
</tr>

<tr>
    <td>Listing format for the item</td>
    <td>
        <select name='ebay_format' class="form-control">
            <option value='Auction (default)' {if $config.ebay.ebay_format eq 'Auction (default)'}selected='selected'{/if}>Auction</option>
            <option value='FixedPrice' {if $config.ebay.ebay_format eq 'FixedPrice'}selected='selected'{/if}>Fixed Price</option>
            <option value='ClassifiedAd' {if $config.ebay.ebay_format eq 'ClassifiedAd'}selected='selected'{/if}>Classified Ad</option>
            <option value='RealEstateAd' {if $config.ebay.ebay_format eq 'RealEstateAd'}selected='selected'{/if}>Real Estate Ad</option>
        </select>
    </td>
</tr>

<tr>
    <td>Location of the item</td>
    <td>
        <input name='ebay_location' class="form-control" type='text' value='{$ebay_location}' />
    </td>
</tr>

<tr>
    <td>PayPal accepted</td>
    <td>
        <input name='ebay_paypal_accepted' class="form-control" type="checkbox" value="Y" onchange="check_paypal_accepted(this);" {if $config.ebay.ebay_paypal_accepted eq 'Y'}checked='checked'{/if}/>
    </td>
</tr>
<tr>
    <td>PayPal Email address</td>
    <td>
        <input name='ebay_paypal_email_address' class="form-control" id='ebay_paypal_email_address' type="text" {if $config.ebay.ebay_paypal_accepted eq 'N'}disabled="disabled"{/if} value="{$config.ebay.ebay_paypal_email_address}"/>
    </td>
</tr>

<tr>
    <td>Immediate pay required</td>
    <td>
        <input name='ebay_immediate_pay_required' class="form-control" type="checkbox" value="Y" {if $config.ebay.ebay_immediate_pay_required eq 'Y'}checked='checked'{/if}/>
    </td>
</tr>


<tr>
    <td>Dispatch time max (Number of business days you usually take to
prepare an item for dispatching)</td>
    <td>
        <select name='ebay_dispatch_time_max' class="form-control">
            <option value='1' {if $config.ebay.ebay_dispatch_time_max eq '1'}selected='selected'{/if}>1</option>
            <option value='2' {if $config.ebay.ebay_dispatch_time_max eq '2'}selected='selected'{/if}>2</option>
            <option value='3' {if $config.ebay.ebay_dispatch_time_max eq '3'}selected='selected'{/if}>3</option>
            <option value='4' {if $config.ebay.ebay_dispatch_time_max eq '4'}selected='selected'{/if}>4</option>
            <option value='5' {if $config.ebay.ebay_dispatch_time_max eq '5'}selected='selected'{/if}>5</option>
            <option value='10' {if $config.ebay.ebay_dispatch_time_max eq '10'}selected='selected'{/if}>10</option>
            <option value='15' {if $config.ebay.ebay_dispatch_time_max eq '15'}selected='selected'{/if}>15</option>
            <option value='20' {if $config.ebay.ebay_dispatch_time_max eq '20'}selected='selected'{/if}>20</option>
            <option value='30' {if $config.ebay.ebay_dispatch_time_max eq '30'}selected='selected'{/if}>30</option>
        </select>
    </td>
</tr>

<tr>
    <td>Indicates that a buyer can return an item</td>
    <td>
        <select name='ebay_returns_accepted_option' class="form-control">
            <option value='ReturnsAccepted' {if $config.ebay.ebay_returns_accepted_option eq 'ReturnsAccepted'}selected='selected'{/if}>Returns Accepted</option>
            <option value='ReturnsNotAccepted' {if $config.ebay.ebay_returns_accepted_option eq 'ReturnsNotAccepted'}selected='selected'{/if}>Returns Not Accepted</option>
        </select>
    </td>
</tr>

</table>

</div>


{include file="common/subheader.tpl" title=$lng.lbl_export_set}

<div class='form-group'>
	<div class="col-xs-12">{include file='elements/widget_set.tpl'}</div>
</div>

{include file="common/subheader.tpl" title=$lng.lbl_export_files}

<div class='form-group'>
<div class="col-xs-12">
	<div id="export_files_container">
		{if $ebay_avail_export_files}
			{foreach from=$ebay_avail_export_files item=file}
				<a href="{$file.path}">{$file.name}</a>&nbsp;<a href="javascript:delete_export_file('{$file.name}');"><img src="{$current_location}/skins/images/delete_cross.gif"></a><br>
			{/foreach}
		{else}
			No files
		{/if}
	</div>
</div>
</div>

</div>

<div class="buttons"><input type="submit" value="Create" class="btn btn-green push-20" /></div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_ebay_export}

<script type="text/javascript">
<!--
{literal}
	function check_paypal_accepted(elem) {

		if (elem.checked) {
			$('#ebay_paypal_email_address').prop('disabled','');
		}
		else {
			$('#ebay_paypal_email_address').prop('disabled','disabled');
		}
	}
	
	function delete_export_file(file) {
		ajaxGet('index.php?target=ebay_export&mode=delete_file&file=' + file, 'export_files_container');
	}
{/literal}
-->
</script>
