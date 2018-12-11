{capture name=section}
{capture name=block}

{$lng.txt_amazon_export_note|default:'txt_amazon_export_note'}


<form method="post" name="amazon_export_form" id="amazon_export_form" class="form-horizontal">
<input type="hidden" name="mode" value="export" />



<div class='form-group'>
<label class="col-xs-12">Export type</label>
<div class="col-xs-12">
	<div class="radio"><label><input type='radio' name='export_type' value='PaQ' checked="checked" /> Price And Quantity</label></div>
   	<div class="checkbox"><label><input type='checkbox' name='price' value='1' checked="checked" /> Price </label></div>
   	<div class="checkbox"><label><input type='checkbox' name='quantity' value='1' checked="checked" /> Quantity</label></div>
	<div class="radio"><label><input type='radio' name='export_type' value='InvLoad' /> Inventory Loader </label></div>
</div>
</div>

{include file="common/subheader.tpl" title="Export options"}
<div class="box">
<table class="table table-striped dataTable vertical-center">

<tr>
    <td>Action for this export [add-delete]</td>
    <td>
        <select name='add-delete' class="form-control">
            <option value='a' selected='selected'>Update/Add on amazon</option>
            <option value='d'>Delete from amazon stock (leave product details)</option>
            <option value='x'>Remove product details from amazon</option>
        </select>
    </td>
</tr>



<tr>
    <td>Product ID type [product-id-type]</td>
    <td>
        <select name='product_id_type' class="form-control">
            <option value=''  {if $config.amazon.product_id_type eq ''}selected='selected'{/if}>Not specified</option>
            <option value='1' {if $config.amazon.product_id_type eq '1'}selected='selected'{/if}>ASIN</option>
            <option value='2' {if $config.amazon.product_id_type eq '2'}selected='selected'{/if}>ISBN</option>
            <option value='3' {if $config.amazon.product_id_type eq '3'}selected='selected'{/if}>UPC</option>
            <option value='4' {if $config.amazon.product_id_type eq '4'}selected='selected'{/if}>EAN</option>
        </select>
    </td>
</tr>
<tr>
    <td>Product ID field</td>
    <td>
        <input name='product_id' type='text' value="{$config.amazon.product_id}" class="form-control" />
    </td>
</tr>



<tr>
    <td>Item condition field</td>
    <td>
        <input name='item_condition' type='text' value='{$config.amazon.item_condition}' class="form-control" />
    </td>
</tr>
<tr>
    <td>Default item condition [item-condition]</td>
    <td>
        <select name='default_item_condition' class="form-control">
            <option value=''   {if $config.amazon.default_item_condition eq '' }selected='selected'{/if}>Not specified</option>
            <option value='1'  {if $config.amazon.default_item_condition eq '1'}selected='selected'{/if}>Used; Like New</option>
            <option value='2'  {if $config.amazon.default_item_condition eq '2'}selected='selected'{/if}>Used; Very Good</option>
            <option value='3'  {if $config.amazon.default_item_condition eq '3'}selected='selected'{/if}>Used; Good</option>
            <option value='4'  {if $config.amazon.default_item_condition eq '4'}selected='selected'{/if}>Used; Acceptable</option>
            <option value='5'  {if $config.amazon.default_item_condition eq '5'}selected='selected'{/if}>Collectible; Like New</option>
            <option value='6'  {if $config.amazon.default_item_condition eq '6'}selected='selected'{/if}>Collectible; Very Good</option>
            <option value='7'  {if $config.amazon.default_item_condition eq '7'}selected='selected'{/if}>Collectible; Good </option>
            <option value='8'  {if $config.amazon.default_item_condition eq '8'}selected='selected'{/if}>Collectible; Acceptable </option>
            <option value='9'  {if $config.amazon.default_item_condition eq '9'}selected='selected'{/if}>Not used</option>
            <option value='10' {if $config.amazon.default_item_condition eq '10'}selected='selected'{/if}>Refurbished (for computers, kitchen &amp; housewares, electronics, and camera &amp; photo only)</option>
            <option value='11' {if $config.amazon.default_item_condition eq '11'}selected='selected'{/if}>New</option>
        </select>
    </td>
</tr>



<tr>
    <td>Ship internationally field</td>
    <td>
        <input name='ship_internationally' type='text' class="form-control" value='{$config.amazon.ship_internationally}' />
    </td>
</tr>
<tr>
    <td>Default "ship internationally" param [will-ship-internationally]</td>
    <td>
        <select name='default_ship_internationally' class="form-control">
            <option value='0' {if $config.amazon.default_ship_internationally eq '0'} selected='selected'{/if}>Not specified</option>
            <option value='Y' {if $config.amazon.default_ship_internationally eq 'Y'}selected='selected'{/if}>{$lng.lbl_yes}</option>
            <option value='N' {if $config.amazon.default_ship_internationally eq 'N'}selected='selected'{/if}>{$lng.lbl_no}</option>
        </select>
    </td>
</tr>



<tr>
    <td>Expedited shipping field</td>
    <td>
        <input name='expedited_shipping' class="form-control" type='text' value='{$config.amazon.expedited_shipping}' />
    </td>
</tr>
<tr>
    <td>Default "expedited shipping" param [expedited-shipping]</td>
    <td>
        <input name='default_expedited_shipping' class="form-control" type='text' value='{$config.amazon.default_expedited_shipping}' />
    </td>
</tr>

<tr>
    <td>Standard plus field</td>
    <td>
        <input name='standard_plus' class="form-control" type='text' value='{$config.amazon.standard_plus}' />
    </td>
</tr>
<tr>
    <td>Default "Standard plus" param [standard-plus]</td>
    <td>
        <select name='default_standard_plus' class="form-control">
            <option value='0' {if $config.amazon.default_standard_plus eq '0'} selected='selected'{/if}>Not specified</option>
            <option value='Y' {if $config.amazon.default_standard_plus eq 'Y'}selected='selected'{/if}>{$lng.lbl_yes}</option>
            <option value='N' {if $config.amazon.default_standard_plus eq 'N'}selected='selected'{/if}>{$lng.lbl_no}</option>
        </select>
    </td>
</tr>



<tr>
    <td>Item note field [item-note]</td>
    <td>
        <input name='item_note' class="form-control" type='text' value='{$config.amazon.item_note|default:""}' />
    </td>
</tr>



<tr>
    <td>Fullfillment center ID [fulfillment-center-id]</td>
    <td>
        <input name='fulfillment_center_id' class="form-control" type='text' value='{$config.amazon.fulfillment_center_id|default:""}' />
    </td>
</tr>

<tr>
    <td>amazon.com's standard code to identify the tax
properties of a product. [product-tax-code]</td>
    <td>
        <input name='default_product_tax_code' class="form-control" type='text' value='{$config.amazon.default_product_tax_code|default:""}' />
    </td>
</tr>

<tr>
    <td>Leadtime to ship [leadtime-to-ship]</td>
    <td>
        <input name='default_leadtime_to_ship' class="form-control" type='text' value='{$config.amazon.default_leadtime_to_ship|default:""}' />
    </td>
</tr>
</table>

</div>

<div class='form-group'>
<label class="col-xs-12">Export set</label>
<div class="col-xs-12">{include file='elements/widget_set.tpl'}</div>
</div>


<div class="buttons"><input type="submit" value="Create" class="btn btn-green push-20" /></div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_amazon_export}
