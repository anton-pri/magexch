<script type="text/javascript" language="JavaScript 1.2">
<!--
var his = new Array();
var alert_message = "{$lng.lbl_wrong_formula|strip_tags|escape:javascript}";
-->
</script>
{include_once_src file='main/include_js.tpl' src='js/tax_formula.js'}

<table class="table dataTable vertical-center">
<tr>
	<td><input class="form-control" type="text" size="70" id="{$name}" name="{$name}" value="={$value}" readonly="readonly" /></td>


	<td align="right">
<input type="button" class="btn btn-default" value="{$lng.lbl_undo|strip_tags:false|escape}" onclick="javacript: undoFormula('{$name}', 'U');" />
<input type="button" class="btn btn-default" value="{$lng.lbl_redo|strip_tags:false|escape}" onclick="javacript: undoFormula('{$name}', 'R');" />
<input type="button" class="btn btn-default" value="{$lng.lbl_clear|strip_tags:false|escape}" onclick="javacript: addElm('{$name}', '=', '=');" />
	</td>
</tr>

<tr>
	<td colspan="2">
<input type="button" class="btn btn-default" value=" + " onclick="javascript: addElm('{$name}', '+', 'O');" />
<input type="button" class="btn btn-default" value=" - " onclick="javascript: addElm('{$name}', '-', 'O');" />
<input type="button" class="btn btn-default" value=" * " onclick="javascript: addElm('{$name}', '*', 'O');" />
<input type="button" class="btn btn-default" value=" / " onclick="javascript: addElm('{$name}', '/', 'O');" />
	</td>
</tr>

<tr>
	<td>
	<select id="unit_{$name}" class="form-control">
	<option></option>
{foreach key=key item=item from=$taxes_units}
		<option value="{$key}">{$key}{if $key ne $item} ({$item}){/if}</option>
{/foreach}
	</select>
	</td>
	<td>
	<input type="button" class="btn btn-default" value="{$lng.lbl_add|strip_tags:false|escape}" onclick="javascript: if(document.getElementById('unit_{$name}').value != '') addElm('{$name}', document.getElementById('unit_{$name}').value, 'V');" />
	</td>
</tr>

<tr> 
	<td>
	<input type="text" id="value_{$name}" class="form-control" />
	</td>
	<td>
	<input type="button" class="btn btn-default" size="8" value="{$lng.lbl_add|strip_tags:false|escape}" onclick="javascript: document.getElementById('value_{$name}').value = (isNaN(parseFloat(document.getElementById('value_{$name}').value)) ? '' : parseFloat(document.getElementById('value_{$name}').value)); if (document.getElementById('value_{$name}').value != '') addElm('{$name}', document.getElementById('value_{$name}').value, 'V');" />
	</td> 
</tr> 

</table>
