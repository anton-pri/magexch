{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="print_barcodes">
<input type="hidden" name="action" value="print_barcode">
{if $current_target eq 'products'}
<input type="hidden" name="product_id" value="{$product.product_id}" />
{else}
<input type="hidden" name="doc_id" value="{$doc.doc_id}" />
{/if}
<div class="input_field_1">
    <label>{$lng.lbl_template}</label>
    {include file='main/select/barcode_template.tpl' name='print[template_id]'}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_print_columns}</label>
    <input type="text" name="print[cols_from]" size="5" /> - 
    <input type="text" name="print[cols_to]" size="5"/>
</div>
<div class="input_field_1">
    <label>{$lng.lbl_print_rows}</label>
    <input type="text" name="print[rows_from]" size="5" /> -
    <input type="text" name="print[rows_to]" size="5" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_labels_amount}</label>
    <input type="text" name="print[amount]" size="5" />
</div>
<script language="javascript">
var lng_lbl_bar_code_select_template = '{$lng.lbl_bar_code_select_template|escape:javascript}';
{literal}
function cw_check_template() {
    if (document.getElementById('printtemplate_id').value) 
        cw_submit_form('print_barcodes'); 
    else 
        alert(lng_lbl_bar_code_select_template);
}
{/literal}
</script>
<div class="buttons">
{include file='buttons/button.tpl' href="javascript: cw_check_template();" button_title=$lng.lbl_print}
</div>
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_bar_codes}

