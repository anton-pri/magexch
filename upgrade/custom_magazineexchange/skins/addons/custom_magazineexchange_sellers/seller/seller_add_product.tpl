{literal}
<script type="text/javascript">
//<![CDATA[

  function popup_category_selection () {
    if ($('#category_dialog').length==0)
      $('body').append('<div id="category_dialog"></div>');
      // Load iframe with category selector into dialog
      $('#category_dialog').html("<iframe frameborder='no' width='828' height='325' src='index.php?target=seller_category_selector'></iframe>");
      // Show dialog
      sm('category_dialog', 840, 365, true, 'Select category');
  }

  function set_selected_category(a, b) {
    console.log('selected vals', a, b);

    if (b) {
      if (Array.isArray(b))
        $('div.category_path.path').html(b.join('&nbsp;<span>></span>&nbsp;'));
      else
        $('div.category_path.path').html(b);
    }


    hm('category_dialog');
    $('#category_id').val(a && Array.isArray(a) ? a.join('|') : '');
  }

  function get_selected_category() {
    const cats = $('#category_id').val();
    if (cats != '') {
      return cats.split('|');
    } else {
      return [];  
    }  
  }

  function get_selected_category_string() {
    const cats = $('#category_id').val();
    if (cats != '') {
      return $('div.category_path.path').html();
    } else {
      return '';  
    } 
  }  

  function cw_seller_add_product_submit_form(form_id, action = null) {

    var required_err = false;
    var required_err_text = [];
    const checked_field_types = ['input[type="hidden"]', 'input[type="text"]'];
    $('div.form-group.required').each(function() {
      var this_fg = this;

      for (var ftx in checked_field_types) {
        $(this).find(checked_field_types[ftx]).each(function(){
          if ($.trim($(this).val()).length == 0) {
              required_err = true;
              required_err_text.push($.trim($(this_fg).find('label').first().text())); 
          }
        });
      }

      $(this).find('div.seller_product_images').each(function() {
        const images_count = $(this).find('img').length;
        if (images_count == 0) {
          required_err = true;
          required_err_text.push($.trim($(this_fg).find('label').first().text()));
        }  
      });

    });

    if (!required_err) {
      if (action)
        cw_submit_form(form_id, action);  
      else  
        cw_submit_form(form_id);  
    } else {
      alert("Please fill in the required fields: \n\"" + required_err_text.join('",\n"') + "\"");
    } 
  }

	$(document).ready(function() {
    $(".category_path.browse").on('click', function(e) {
      popup_category_selection();
    });
	});
//]]>
</script>
{/literal}

<script type="text/javascript">
//<![CDATA[
  {if $product.product_id && $seller_product_run_preview eq 'Y'}
  $(document).ready(function() {ldelim}
    var win = window.open('../index.php?target=product&product_id={$product.product_id}', '_blank'); 
    if (win)
      win.focus();
    else 
      alert('Cannot open the product preview in new tab. Please check if popup windows are allowed in your browser for the magazineexchange.co.uk host');  
	{rdelim});
  {/if}
//]]>
</script>  

{capture name=dialog}
  <div style="margin-left: 1%;">
    <div style="float:left; width:99%;" class="block block-themed animated fadeIn">
      <div class="block-header bg-green">
        <h3 style="text-align: center;" class="block-title">{$lng.lbl_add_new_product|default:'Add new product'}</h3>
      </div>
      <div class="jasellerblock-content">

        <div class="block seller_add_product">

            <div class="block-content" style="padding:20px !important">
              <form name="seller_add_product" action="index.php?target=seller_add_product" method="POST">
                <input type="hidden" name="mode" value="{if $product.product_id}update{else}add{/if}" />
                <input type="hidden" name="action" value="" />
                <input type="hidden" name="product_id" value="{$product.product_id}" />

                <div class="form-group category-selector required">
                  <label class="col-xs-12" {*style="width:auto; padding-right:0"*}>Product Category&nbsp;</label>{$lng.lbl_seller_add_new_product_note}
                  <input type="hidden" id="category_id" name="product_data[category_id]" value="{$product.category_id|default:''}" />
                  <div class="category_path path" >
                    {$product.category_path|default:"not selected"}
                  </div>                

                  <div class="category_path browse">
                    Browse Categories
                  </div>
                </div>

                <div class="form-group short-input required">
                  <label class="required col-xs-12" {*style="width:auto; padding-right:0"*}>
                    {$lng.lbl_product_full_name|default:'Product Full Name'}
                  </label>
                  {$lng.lbl_seller_product_full_name_note}
                  <div class="col-xs-12 col-md-4" {*style="float:none;"*}>
                    <input type="text" name="product_data[product]" value="{$product.product}" size="40" />
                  </div>
                </div>              

                <div class="form-group short-input">
                  <label class="col-xs-12">
                    {$lng.lbl_product_short_name|default:'Product Short Name'}
                  </label>
                  {$lng.lbl_seller_product_short_name_note}
                  <div class="col-xs-12 col-md-4">
                    <input type="text" name="product_data[attributes][magexch_product_short_product]" value="{$attributes.magexch_product_short_product.value}" size="40" />
                  </div>  
                </div>              

                <div class="form-group image-selector required">
                  <label class="col-xs-12 required">
                    {$lng.lbl_main_product_image|default:'Main Product Image'}
                  </label>
                  {$lng.lbl_seller_product_main_image_note}
                  {include 
                    file="addons/custom_magazineexchange_sellers/seller/seller_product_images.tpl" 
                    in_type="products_images_det" 
                    product_id=$product.product_id}
                </div>

                <div class="form-group image-selector">
                  <label class="col-xs-12">
                    {$lng.lbl_alternative_thumbnail_image|default:'Alternative Thumbnail Image (Optional)'}
                  </label>
                  {$lng.lbl_seller_product_thumbnail_image_note}
                  {include 
                    file="addons/custom_magazineexchange_sellers/seller/seller_product_images.tpl" 
                    in_type="products_images_thumb" 
                    product_id=$product.product_id}
                </div>

                <div class="form-group image-selector">
                  <label class="col-xs-12">
                    {$lng.lbl_additional_product_images|default:'Additional Product Images'}
                  </label>
                  {$lng.lbl_seller_product_additional_images_note}
                  {include 
                    file="addons/custom_magazineexchange_sellers/seller/seller_product_images.tpl" 
                    in_type="products_detailed_images" 
                    product_id=$product.product_id}
                </div>

                <div class="form-group textarea">
                  <label class="col-xs-12">
                    {$lng.lbl_left_description_box|default:"Left Description Box ('Contents Listing' for  Magazine issues)"}
                  </label>
                  {$lng.lbl_seller_product_left_description_note}
                  <div class="col-xs-12 col-md-4">
                    <textarea name="product_data[fulldescr]"  rows="10" cols="80"/>{$product.fulldescr}</textarea>
                  </div>  
                </div> 

                <div class="form-group textarea">
                  <label class="col-xs-12">
                    {$lng.lbl_right_description_box|default:"Right Description Box ('Article snippets' for  Magazine issues) (Optional)"}
                  </label>
                  {$lng.lbl_seller_product_left_description_note}
                  <div class="col-xs-12 col-md-4">
                    <textarea name="product_data[descr]"  rows="10" cols="80"/>{$product.descr}</textarea>
                  </div>  
                </div> 

                <div class="form-group small-input required">
                  <label class="col-xs-12">
                    {$lng.lbl_item_weight|default:'Item Weight (kg)'}
                  </label>
                  <div class="col-xs-12 col-md-4">
                    <input type="text" name="product_data[weight]" value="{$product.weight}" size="8" />
                  </div>  
                </div>  

                <div class="form-group small-input required">
                  <label class="col-xs-12">
                    {$lng.lbl_seller_number_of_pages|default:'Number of Pages (Magazine issues only)'}
                  </label>
                  <div class="col-xs-12 col-md-4">
                    <input type="text" name="product_data[attributes][magexch_product_NUMBER_PAGES]" value="{$attributes.magexch_product_NUMBER_PAGES.value}" size="8" />
                  </div>  
                </div>  


                <div class="form-buttons">
                  <span>
                      <input 
                        type="button" 
                        class="btn btn-minw btn-default btn-green push-5-t" 
                        value="{$lng.lbl_preview}" 
                        onclick="javascript: cw_seller_add_product_submit_form('seller_add_product', 'preview');"
                    />
                  </span>
                  <span style="margin-left:20px">
                    <input 
                        type="button" 
                        class="btn btn-minw btn-default btn-green push-5-t" 
                        value="{$lng.lbl_publish}" 
                        onclick="javascript: cw_seller_add_product_submit_form('seller_add_product', 'publish');"
                    />
                  <!--
                    <input 
                        type="button" 
                        class="btn btn-minw btn-default btn-green push-5-t" 
                        value="{$lng.lbl_preview}" 
                        onclick="javascript:var win = window.open('../index.php?target=product&product_id={$product.product_id}', '_blank'); win.focus();"
                    />
                  -->  
                  </span>
                </div>
              </form>
            </div>  
          </div>
        </div>  
      </div>
    </div>
  </div>
{/capture}
{include file="admin/wrappers/section.tpl" title="" content=$smarty.capture.dialog extra='width="100%"'}