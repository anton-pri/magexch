{if $included_tab eq 'clients'}
<script language="Javascript">
{literal}
function cw_products_clients_search(product_id) {
    substring = document.getElementById('products_clients_substring').value;
    fromdate = document.getElementById('products_clients_fromdate').value;
    todate = document.getElementById('products_clients_todate').value;
    document.getElementById('products_clients').src="index.php?target=products_clients&mode=search_clients&product_id="+product_id+"&substring="+substring+'&fromdate='+fromdate+'&todate='+todate;
}
{/literal}
</script>

{capture name="section"}
<div class="form-horizontal">
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_substring}</label>
    <div  class="col-xs-12"><input class="form-control" type="text" id="products_clients_substring" size="32" maxlength="32" value="{$products_clients.substring|escape}" /></div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_date}</label>
    <div class="col-xs-12 form-inline">
		<div class="form-group">{include file='main/select/date.tpl' name='products_clients_fromdate' value=$products_clients.creation_date_start}</div>
		<div class="form-group"> - </div>
		<div class="form-group">{include file='main/select/date.tpl' name='products_clients_todate' value=$products_clients.creation_date_end}</div>
	</div>
</div>
<div class="form-group">
<iframe width="100%" height="250" id="products_clients" src="index.php?target=products_clients&product_id={$product_id}" style="border: 1px solid #ddd;"></iframe>
</div>
</div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_products_clients_search(`$product_id`)" style="btn-green push-20"}

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_search_for_clients content=$smarty.capture.section}
{elseif $included_tab eq "error"}
{capture name=section2}
<div class="dialog_title">{$lng.txt_cant_create_product_warning}</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_register_warehouse href="index.php?target=users_P"}
{/capture}
{include file='admin/wrappers/block.tpl' content=$smarty.capture.section2 title=$lng.lbl_warning}

{elseif $included_tab eq "attributes"}

{/if}
