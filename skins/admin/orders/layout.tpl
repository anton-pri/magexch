{include file='common/subheader.tpl' title=$lng.lbl_available_templates}
<form action="index.php?target={$current_target}&mode={$mode}" method="post" name="templates_form">
<input type="hidden" name="doc_id" value="{$doc_id}" />
<input type="hidden" name="action" value="change_template" />

<div class="input_field_1">
    <label>{$lng.lbl_current_template}</label>
    {include file='main/select/template.tpl' name='template[layout_id]' value=$layout_data.layout_id onchange="javascript: cw_submit_form('templates_form');"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('templates_form', 'delete_template')"}
</div>

<div class="input_field_1">
    <label>{$lng.lbl_create_new}</label>
    <input type="text" name="template[title]" value="" />
    {include file='buttons/button.tpl' button_title=$lng.lbl_create href="javascript:cw_submit_form('templates_form', 'create_template')"}
</div>

<div class="input_field_1">
    <label>{$lng.lbl_copy_layout_from}</label>
    {include file='main/select/template.tpl' name='template[source_layout_id]' value=0 is_please_select=1 layouts=$copy_layouts}
    {include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('templates_form', 'copy_layout_template')"}
</div>

</form>

{include file='common/subheader.tpl' title=$lng.lbl_template_properties}

<form action="index.php?target={$current_target}" method="post" name="template_prop_form">
<input type="hidden" name="doc_id" value="{$doc_id}" />
<input type="hidden" name="action" value="set_template" />
<div class="input_field_1">
    <label>{$lng.lbl_products_per_page}</label>
    <input type="input" name="label_data[products_per_page]" value="{$layout_data.data.products_per_page|escape}">
</div>
<div class="input_field_1">
    <label>{$lng.lbl_product_fields}</label>
<table class="header">
<tr>
    <td id="iml_box_1">{include file='main/select/doc_layout_element.tpl' name='label_data[elements][0]'}</td>
    <td id="iml_add_button">{include file='main/multirow_add.tpl' mark='iml'}</td>
</tr>
</table>
    </div>
{if $layout_data.data.elements}
<script type="text/javascript">
{foreach from=$layout_data.data.elements item=element}
add_inputset_preset('iml', document.getElementById('iml_add_button'), false,
    [
    {ldelim}regExp: /label_data/, value: '{$element}'{rdelim},
    ]
);
{/foreach}
</script>
{/if}

{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('template_prop_form')"}
</form>

{include file='main/layout/layout.tpl'}
