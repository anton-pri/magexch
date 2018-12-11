{include file='common/subheader.tpl' title=$lng.lbl_template}
<form action="index.php?target={$current_target}" method="post" name="templates_form">
<input type="hidden" name="action" value="set_template" />
<div class="input_field_1">
    <label>{$lng.lbl_choose_barcode_template}</label>
    {include file='main/select/barcode_template.tpl' name='template_id' value=$template_id onchange='this.form.submit();'}
    {include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('templates_form', 'delete')"}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_create}</label>
    <input type="input" name="title" value="">
    {include file='buttons/button.tpl' button_title=$lng.lbl_create href="javascript:cw_submit_form('templates_form', 'create')"}
</div>
</form>

{if $layout}
{include file='common/subheader.tpl' title=$lng.lbl_template_properties}
<form action="index.php?target={$current_target}" method="post" name="template_prop_form">
<input type="hidden" name="action" value="set_template" />
<div class="input_field_easy_0_0">
    <label>{$lng.lbl_page_padding} ({$lng.lbl_left} / {$lng.lbl_right} / {$lng.lbl_top} / {$lng.lbl_bottom})</label>
    <input type="text" name="label_data[page_left]" value="{$layout.data.page_left}" size="4" />
    <input type="text" name="label_data[page_right]" value="{$layout.data.page_right}" size="4" />
    <input type="text" name="label_data[page_top]" value="{$layout.data.page_top}" size="4" />
    <input type="text" name="label_data[page_bottom]" value="{$layout.data.page_bottom}" size="4" />
</div>
<div class="input_field_easy_1_0">
    <label>{$lng.lbl_labels_layout} ({$lng.lbl_rows} x {$lng.lbl_columns})</label>
    <input type="text" name="label_data[rows]" value="{$layout.data.rows}" size="4" />
    <input type="text" name="label_data[cols]" value="{$layout.data.cols}" size="4" />
</div>
<br/>
<div class="input_field_easy_0_0">
    <label>{$lng.lbl_label_padding} ({$lng.lbl_left} / {$lng.lbl_right} / {$lng.lbl_top} / {$lng.lbl_bottom})</label>
    <input type="text" name="label_data[left]" value="{$layout.data.left}" size="4" />
    <input type="text" name="label_data[right]" value="{$layout.data.right}" size="4" />
    <input type="text" name="label_data[top]" value="{$layout.data.top}" size="4" />
    <input type="text" name="label_data[bottom]" value="{$layout.data.bottom}" size="4" />
</div>
<div class="input_field_easy_1_0">
    <label>{$lng.lbl_label_size} ({$lng.lbl_width} x {$lng.lbl_height} / {$lng.lbl_border})</label>
    <input type="text" name="label_data[width]" value="{$layout.data.width}" size="4" />
    <input type="text" name="label_data[height]" value="{$layout.data.height}" size="4" />
    <input type="checkbox" name="label_data[border]" value="1"{if $layout.data.border} checked{/if} />
</div>
<br/>

<div class="input_field_easy_0_0">
    <label>{$lng.lbl_barcode_height} ({$lng.lbl_product}/{$lng.lbl_supplier}{if $addons.sn}/{$lng.lbl_serial_number}{/if})</label>
    <input type="text" name="label_data[barcode_height]" value="{$layout.data.barcode_height}" size="4" />
    <input type="text" name="label_data[barcode_height_supplier]" value="{$layout.data.barcode_height_supplier}" size="4" />
{if $addons.sn}
    <input type="text" name="label_data[barcode_height_sn]" value="{$layout.data.barcode_height_sn}" size="4" />
{/if}
</div>
<div class="input_field_easy_1_0">
    <label>{$lng.lbl_barcode_width} ({$lng.lbl_product}/{$lng.lbl_supplier}{if $addons.sn}/{$lng.lbl_serial_number}{/if})</label>
    <input type="text" name="label_data[barcode_width]" value="{$layout.data.barcode_width}" size="4" />
    <input type="text" name="label_data[barcode_width_supplier]" value="{$layout.data.barcode_width_supplier}" size="4" />
{if $addons.sn}
    <input type="text" name="label_data[barcode_width_sn]" value="{$layout.data.barcode_width_sn}" size="4" />
{/if}
</div>
<br/>
<div class="input_field_easy_0_0">
    <label>{$lng.lbl_display_price_with_vat}</label>
    <input type="checkbox" name="label_data[use_tax]" value="1"{if $layout.data.use_tax} checked{/if} />
</div>
<br/>

{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('template_prop_form', 'update')"}
</form>

{include file='main/layout/layout.tpl'}
{/if}
