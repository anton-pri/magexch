<script>
{literal}
$(document).ready(function(){
	radioclick = function(){
		var act = $('input[name=action]:checked', '#bundle_form').val();
		if (act == 'build') {
			$('#filter_without').show();
			$('#category_src_div').show();
			$('#products_div').show();
			$('#discount_div').show();
		} else {
			$('#filter_without').hide();
			$('#category_src_div').hide();
			$('#products_div').hide();
			$('#discount_div').hide();			
		}
	};
	$('input[name=action]').click(radioclick);
	radioclick();
});
{/literal}
</script>

{capture name=section}
{capture name=block}

<form method='POST' name='bundle_form' id='bundle_form' class="form-horizontal">
<div class='form-group'>
	<label class="col-xs-12">Action</label>
	<div class="col-xs-12">
		<div class="radio"><label><input type='radio' name='action' value='drop' /> Drop bundles &nbsp;</label></div>
		<div class="radio"><label><input type='radio' name='action' value='enable' /> Enable bundles &nbsp;</label></div>
		<div class="radio"><label><input type='radio' name='action' value='disable' /> Disable bundles &nbsp;</label></div>
		<div class="radio"><label><input type='radio' name='action' value='build' /> Rebuild bundles</label></div>
	</div>
</div>

<div class='form-group'>
	<label class="col-xs-12">Apply to</label>
	<div class="col-xs-12">
		<div class="checkbox"><label><input type='checkbox' name='filter[auto]' value='1' /> Products with automatic created bundles </label></div>
		<div class="checkbox"><label><input type='checkbox' name='filter[manual]' value='1' /> Products with manually created bundles </label></div>
		<div class="checkbox" id='filter_without'><label><span><input type='checkbox' name='filter[without]' value='1' /> Products without assigned bundles</span> </label></div>
	</div>
</div>

<div class='form-group'>
	<label class="col-xs-12">Process products from category</label>
    <div class="col-xs-12">{include file='admin/select/category.tpl' name='category'}</div>
</div>
<div class='form-group' id='category_src_div'>
	<label class="col-xs-12">Add product to bundle from category</label>
    <div class="col-xs-12">{include file='admin/select/category.tpl' name='category_src'}</div>
</div>
<div class='form-group' id='products_div'>
	<label class="col-xs-12">Products in bundle</label>
 	<div class="col-xs-12"><input type='text' class='number micro' name='products_number' value='2' /> </div>
</div>
<div class='form-group' id='discount_div'>
	<label class="col-xs-12">{$lng.lbl_discount}</label>
	<div class="col-xs-12">
  		<input type='text' class='number micro' name='discount' />
		<select name="disctype">
			<option value="2">{$lng.lbl_percent}, %</option>
        	<option value="1">{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
		</select>
	</div>
</div>

<div class="buttons">
{include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('bundle_form');" button_title=$lng.lbl_apply|escape acl=$page_acl style="btn-green push-20"}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_discount_bundles}
