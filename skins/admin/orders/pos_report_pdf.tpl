<form name="warehouses_form" action="index.php" method="post">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="mode" value="pdf" />
<input type="hidden" name="action" value="generate" />
<div class="input_field_1">
    <label>{$lng.lbl_date}</label>
{include file='main/select/date.tpl' name='posted_data[from_date]' value=$search_prefilled.from_date} -
{include file='main/select/date.tpl' name='posted_data[to_date]' value=$search_prefilled.to_date}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_warehouse}</label>
    {include file='main/select/warehouse.tpl' name='posted_data[warehouse_customer_id]' value=$search_prefilled.warehouse_customer_id}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_category}</label>
    {include file='main/select/category.tpl' name='posted_data[category_id][]' value=$search_prefilled.category_id_orig multiple=1}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_columns}</label>
<table class="header">
<tr>
    <td id="iml_box_1">{include file='main/select/pos_report_columns.tpl' name='posted_data[elements][0]' value=$search_prefilled.elements.0}</td>
    <td id="iml_add_button">{include file='main/multirow_add.tpl' mark='iml'}</td>
</tr>
</table>
{if $search_prefilled.elements}
<script type="text/javascript">
{foreach from=$search_prefilled.elements key=index item=elem}
{if $index != 0}
add_inputset_preset('iml', document.getElementById('iml_add_button'), false, 
    [{ldelim}regExp: /posted_dataelements/, value: '{$elem}'{rdelim}]
);
{/if}
{/foreach}
</script>
{/if}
</div>

{include file='buttons/button.tpl' button_title=$lng.lbl_show href="javascript: cw_submit_form('warehouses_form');"}
</form>
