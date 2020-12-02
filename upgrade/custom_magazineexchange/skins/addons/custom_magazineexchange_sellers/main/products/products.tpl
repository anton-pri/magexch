{if $products}
{if $current_target eq 'digital_products'}
<style type="text/css">{literal}
.error_required { border: solid 1px red; background-color: #FFE3E3; }
#seller_products_list td { vertical-align:top; }
#seller_products_list textarea {height:85px;}
{/literal}</style>



<script type="text/javascript">
var price_low_limit = {$config.custom_magazineexchange_sellers.mag_seller_minimal_price|default:'0.00'};
var price_currency_symbol = '{$config.General.currency_symbol}';

var fields_err = false;
{literal}
function cw_check_digital_products_data() {
    var focus_on_first = false;
    fields_err = false;
    $(".error_required").removeClass("error_required"); 
    $("#seller_products_list tr").each(function () { 
       var tr_elem_msg = '';
       var all_fields_empty = true;
       var empty_elems = [];
       var non_empty_elems = '';
       $(this).find("td").each(function() {
          var elem_msg = '';
          $(this).find('input:text, select, textarea').each(function() {
                  var elem_value;
                  elem_value = $(this).val(); 
                  var elem_name; 
                  elem_name = $(this).attr('name'); 

                  if (elem_value.length != 0) {
                      all_fields_empty = false; 
                      non_empty_elems += 'non empty element' + elem_name;
                  }

                  if (elem_value.length == 0 
                       && ((elem_name.indexOf("seller_product_main_file") >= 0) ||
                           (elem_name.indexOf("seller_product_file_type") >= 0))) {
                          elem_msg += '\nempty value in element: ';
                          empty_elems.push(this);   
                  }

                  if (elem_name.indexOf("price") >= 0) {
                      var price_val = 0;
                      if (elem_value.length != 0) 
                          price_val = parseFloat(elem_value); 

                      if (price_val < price_low_limit) { 
                          elem_msg += '\nempty value in element: ';
                          empty_elems.push(this);
                      } 
                  }
 
                  elem_msg += $(this).prop('tagName');  
                  if ($(this).prop('tagName')=='INPUT') 
                      elem_msg += ' type=' + $(this).attr('type');
                  elem_msg += ' name=' + elem_name + ', ';
          });
 
          if (elem_msg != '')   
            tr_elem_msg += " td:"+elem_msg;
       });

       if (!all_fields_empty) {
          for (var x in empty_elems) {
              $(empty_elems[x]).parent().addClass("error_required"); 
              fields_err = true; 
          }
       } 
    });

}

$(document).ready(function() {
  $("#process_product_form").on('submit', function(event) {
      if (!fields_err) return;
      alert("Please fill all required fields or leave them empty to delete entry. Minimum allowed price is "+price_currency_symbol+price_low_limit+".");
      event.preventDefault(); 
  });
  setInterval(cw_check_digital_products_data, 400);
});
{/literal}
</script>
{/if}
<table class="table table-striped dataTable vertical-center" width="100%" id="seller_products_list">
<tr>	<th width="10">{*<input type='checkbox' class='select_all' class_to_select='products_item' />*}&nbsp;</th>
    <th>&nbsp;</th>
	<th>{$lng.lbl_product}</th>
{if $current_target ne 'digital_products'}
    <th>{$lng.lbl_product_search_condition}</th>
{else}
    <th>{$lng.lbl_digital_main_file}</th>
    <th>{$lng.lbl_digital_file_type}</th>
    <th>{$lng.lbl_digital_preview_file}</th>
{/if}
    <th>{$lng.lbl_comments}</th>
{if $current_target ne 'digital_products'}
	<th>{$lng.lbl_in_stock}</th>
{/if}
	<th width="100px"><nobr>{$lng.lbl_price} ({$config.General.currency_symbol})</nobr></th>
    <th width="5%">&nbsp;</th>
</tr>

{tunnel func='cw_attributes_get' item_type='SP' item_id=0 assign='sp_attributes'}

{foreach from=$products item=product}
<tr{cycle values=', class="cycle"'}>
	<td>{*<input type="checkbox" name="product_ids[{$product.product_id}]" class="products_item" />*}&nbsp;</td>
    <td>
        {include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_det id="product_thumbnail_`$product.product_id`" html_width='50'}
    </td>
	<td>
        <a href="{$catalogs.customer}/index.php?target=product&product_id={$product.product_id}" target="_blank" class="ProductBlue">{$product.product}</a>
    </td>
{if $current_target ne 'digital_products'}
    <td width="125">
        <select style="width: 100px;" class="form-control" name='posted_data[{$product.product_id}][condition]'>
	  <option value='4' >Select</option>        
	  <option value='0' >New</option>
        <option value='1' >Good</option>
        <option value='2' >Fair</option>
        <option value='3' >Poor</option>
        </select>
    </td>
{else}
    <td>{include file='main/attributes/default_types.tpl' fieldname="posted_data[`$product.product_id`]" attribute=$sp_attributes.seller_product_main_file}</td>  
    <td>{include file='main/attributes/default_types.tpl' fieldname="posted_data[`$product.product_id`]" attribute=$sp_attributes.seller_product_file_type}
    </td>
    <td>{include file='main/attributes/default_types.tpl' fieldname="posted_data[`$product.product_id`]" attribute=$sp_attributes.seller_product_preview_file}</td>
{/if}
    <td width="250"><textarea style="width:100%;" class="form-control" name='posted_data[{$product.product_id}][comments]' rows='2' class='free'></textarea></td>
{if $current_target ne 'digital_products'}
    <td width="40"><input style="width:100%;" type='text'  name='posted_data[{$product.product_id}][quantity]' class="form-control" value='' /></td>
{/if}
	<td width="35" nowrap align="center">
        <input style="width:100%;" type='text' name='posted_data[{$product.product_id}][price]' class="form-control" value='' />
	</td>
    <td>
        {if $product.created_by_current_user}
            <span>
                <input 
                    type="button" 
                    class="btn btn-minw btn-default btn-green push-5-t" 
                    value="{$lng.lbl_edit_page}" 
                    onclick="javascript:var win = window.open('index.php?target=seller_add_product&product_id={$product.product_id}', '_blank'); win.focus();"
                />
            </span>
        {else}
        &nbsp;    
        {/if}
    </td>
</tr>
{if $product.seller_data}
{foreach from=$product.seller_data item=seller_data}
<tr{cycle values=', class="cycle"'}>
	<td style="color:Black; text-align: center; vertical-align:middle; background: linear-gradient(to right, rgba(254,204,5,1), rgba(254,204,5,0)); font-size:10pt; !important; font-weight:600; border: 0px solid black;" colspan="2"><input type="checkbox" name="delete_seller_ids[{$seller_data.seller_item_id}]" class="products_item" title="{$lng.lbl_select_to_delete|default:"Select to delete"}" /> <br>{$lng.lbl_delete_listing}</td>
    
	<td>&nbsp;&nbsp;{$product.product}</td>
{if $current_target ne 'digital_products'}
    <td>
        <select style="width: 100px;" class="form-control" name='seller_data[{$seller_data.seller_item_id}][condition]'>
        <option value='4' {if $seller_data.condition eq 4}selected='selected'{/if}>Select</option>
        <option value='0' {if $seller_data.condition eq 0}selected='selected'{/if}>New</option>
        <option value='1' {if $seller_data.condition eq 1}selected='selected'{/if}>Good</option>
        <option value='2' {if $seller_data.condition eq 2}selected='selected'{/if}>Fair</option>
        <option value='3' {if $seller_data.condition eq 3}selected='selected'{/if}>Poor</option>
        </select>
    </td>
{else}
    <td>{include file='main/attributes/default_types.tpl' fieldname="seller_data[`$seller_data.seller_item_id`]" attribute=$seller_data.attributes.seller_product_main_file}</td>
    <td style="text-align:center">{include file='main/attributes/default_types.tpl' fieldname="seller_data[`$seller_data.seller_item_id`]" attribute=$seller_data.attributes.seller_product_file_type}<br /><br />
    <a href="index.php?target=seller_getfile&seller_item_id={$seller_data.seller_item_id}&test_mode=Y" target="_blank">{$lng.lbl_test_download}</a>
    </td>
    <td>{include file='main/attributes/default_types.tpl' fieldname="seller_data[`$seller_data.seller_item_id`]" attribute=$seller_data.attributes.seller_product_preview_file}</td>
{/if}
    <td width="250"><textarea style="width:100%;" class="form-control" name='seller_data[{$seller_data.seller_item_id}][comments]' rows='2' class='free'>{$seller_data.comments}</textarea></td>
{if $current_target ne 'digital_products'}
    <td width=40" ><input style="width:100%;" class="form-control" type='text' name='seller_data[{$seller_data.seller_item_id}][quantity]' class='micro' value='{$seller_data.quantity}' /></td>
{/if}
	<td width="35" nowrap align="center">
        <input style="width:100%;" class="form-control" type='text' name='seller_data[{$seller_data.seller_item_id}][price]' class='micro' value='{$seller_data.price}' />
	</td>
    <td>&nbsp;</td>
</tr>
{/foreach}
{/if}
{/foreach}

</table>
{/if}
