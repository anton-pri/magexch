{jstabs}
default_tab={$js_tab|default:"basic_search"} 
default_template=admin/products/search_form.tpl

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form, 'search');"
style="btn-green push-5-r"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"
style="btn-danger push-5-r"

[add]
<!-- type='button'
title='{$lng.lbl_add_new}'
href='index.php?target=products&mode=add'
style="btn-green" -->

[basic_search]
title="{$lng.lbl_search_products}"
display=always

[add_search]
title="{$lng.lbl_advanced_products_search}"

[prices]
title={$lng.lbl_prices}

[additional_options]
title="{$lng.lbl_additional_options}"

{/jstabs}

{capture name=section1}



<div style="margin-left: 30%;">
<div style="float:left; width:410px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_search_products}</h3></div>
<div class="col-sm-12" style="padding:10px 0 5px 15px;">{$lng.lbl_seller_search}</div>
<div style="width:410px;" class="jasellerblock-content">
<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/sellersearch_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>
</div></div><div>
<div style="margin-left: 500px;"><a href="/help-centre-selling-back-issues.html"><img src="{$AltImagesDir}/admin/Need_Help_Selling_Guide.gif" width="123" height="183" target="_blank"></a></div>
<div style="clear: both;"></div>
</div><br></div>

{*
<div style="width:410px; margin-left: auto; margin-right: auto;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_search_products}</h3></div>
<div class="col-sm-12" style="padding:10px 0 5px 15px;">{$lng.lbl_seller_search}</div>
<div style="width:410px;" class="jasellerblock-content">
<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/sellersearch_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>
</div></div> *}


 


{if $mode eq 'search'}

{if $products}
{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="process_product_form" id="process_product_form" onsubmit="return mag_check_products_fields()">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="navpage" value="{$navpage}" />

      <div class="row">
        <div class="col-sm-12" style="padding:10px 10px 5px 10px;"><div style="float:left;"> {$lng.lbl_seller_search_results}</div><div style="float:right;">
 {include file='common/navigation_counter.tpl'}</div></div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          {include file='main/products/products.tpl' products=$products}
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">{include file='common/navigation.tpl'}</div> </div>

<div id="sticky_content" class="buttons">
<div class="product_buttons">
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form');" class='update' button_title=$lng.lbl_update acl=$page_acl style="btn-green push-5-r push-20"}
{if $usertype eq 'A'}
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'delete');" class='delete' button_title=$lng.lbl_delete_selected acl=$page_acl style="btn-green push-5-r push-20"}
{/if}
<!--{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'list');" class='modify' button_title=$lng.lbl_modify_selected acl=$page_acl style="btn-green push-5-r push-20"}-->
{include file='admin/buttons/button_export.tpl' href="javascript: addToExport();" class='add_to_export' button_title=$lng.add_to_export acl=$page_acl style="btn-green push-5-r push-20"}

{if ($usertype eq 'P' and $accl.1008) or ($usertype eq 'A' and $accl.1202)}
{/if}

{if $usertype eq 'A'}

{*$lng.txt_operation_for_first_selected_only*}
<div class="product_buttons" style="float:right;padding-right:10px;">

{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'preview');" button_title=$lng.lbl_preview_product style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'clone');" button_title=$lng.lbl_clone_product acl=$page_acl style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'links');" button_title=$lng.lbl_generate_html_links style="btn-green push-20"}
</div>
{/if}
</div>


</div>

</form>
{/capture}

{include file="admin/wrappers/jablock.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_search_results}
<br>{cms service_code="Items_for_Sale_bottom"}
{/if}
{/if}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section1 extra='width="100%"' title=$lng.lbl_products local_config='product'}


<script type='text/javascript'>
{literal}
	$(document).ready(function() {
		if (typeof resetSet != "function") {
			ajaxGet('index.php?target=import&mode=export_set&action=getcount');
		}

        var tab_content_id =  $(".tab_content_selected").attr("id");
        var tab_id         = tab_content_id.replace("contents_", "tab_");
        if(tab_id == 'tab_additional_options')
            if(checkInputIsEmpty(tab_content_id))
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", false);
            else
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);


        $('form[name="search_form"]  input[type="text"]').on("input",function(){
            var tab_content_id =  $(".tab_content_selected").attr("id");
            var tab_id         = tab_content_id.replace("contents_", "tab_");
            if($(this).val()!="" && $(this).val()!="0"){
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);
            }else{
                if(checkInputIsEmpty(tab_content_id))
                    $('#'+tab_id +' input[type="checkbox"]').prop("checked", false);
            }
        });

        $('form[name="search_form"] input[type="checkbox"]').on("change",function(){
            var tab_content_id =  $(".tab_content_selected").attr("id");
            var tab_id         = tab_content_id.replace("contents_", "tab_");
            if($(this).is(":checked")){
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);

            }else{
                if(checkInputIsEmpty(tab_content_id))
                    $('#'+tab_id +' input[type="checkbox"]').prop("checked", false);
            }
        });

        $('form[name="search_form"]  select, form[name="search_form"]  input[type="radio"]').on("change",function(){
            var tab_content_id =  $(".tab_content_selected").attr("id");
            var tab_id         = tab_content_id.replace("contents_", "tab_");
            $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);
        });

        $(document).on('click', '.calendar', function(){
            var tab_content_id =  $(".tab_content_selected").attr("id");
            var tab_id         = tab_content_id.replace("contents_", "tab_");
            if(checkInputIsEmpty(tab_content_id))
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", false);
            else
                $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);


        });


        $(document).on('click', '#posted_datacategories_body a', function(){

            setTimeout(function() {
                $('input[name="posted_data[categories][]"]').each(function() {
                    if(this.value!=""){
                        var tab_content_id =  $(".tab_content_selected").attr("id");
                        var tab_id         = tab_content_id.replace("contents_", "tab_");
                        $('#'+tab_id +' input[type="checkbox"]').prop("checked", true);
                        return;
                    }
                });
            }, 500);
        });
    });


function checkInputIsEmpty(tab_content){
    var inputsEmpty = true;

    $('#'+tab_content+' input[type="text"]').each(function() {
        if($(this).val() !="" && $(this).val()!="0"){
            inputsEmpty = false;
        }
    });
    $('#'+tab_content+' input[type="checkbox"]').each(function() {
        if($(this).is(":checked")){
            inputsEmpty = false;
        }
    });
    $('#'+tab_content+' select').each(function() {
        if($(this).prop("selectedIndex") >0){
            inputsEmpty = false;
        }
    });
    return  inputsEmpty;
}

	function addToExport() {
		var ids = [];
		// each checkbox
		$("form[name='process_product_form'] :checkbox:checked").each(function() {
			// get id
			var id = $(this).attr('name').match(/\d+/)[0];
			// if number
			if (!/[^[0-9]/.test(id)) {
				ids.push(id);
			}
		});

		if (ids.length) {
			ajaxGet('index.php?target=import&mode=export_set&action=add&set_ids=' + ids.join(','), "process_product_form", exportCallback);
		}
	}

	function exportCallback(data) {
		// clear checkbox
		$("form[name='process_product_form'] :checkbox:checked").each(function() {
			$(this).prop('checked', '');
		});
	}


    function mag_check_products_fields() {
        var check_result = true;
        $('input[name*="[quantity]"]').each(function(){
            if ($(this).val() == '' || $(this).val() == 0) return true;
            var qty_attr = $(this).attr('name');
            var price_attr = qty_attr.replace('quantity','price');
            var price_field = $('input[name="'+price_attr+'"]');
            $(price_field).css('background-color','white');
            if ($(price_field).val()<=0) {
                $(price_field).css('background-color','red');
                check_result = false;
            }
        });
        if (!check_result) {
            alert('Sorry - items cannot be listed for sale with a negative or zero selling price - please enter a positive value');
        }
        return check_result;

    }
{/literal}
</script>

