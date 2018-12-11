<select name="{$name}" class="form-control">
<option value="0"{if $value eq 0} selected{/if}>{$lng.lbl_cod_passing}</option>
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_cod_keeping}</option>
<option value="2"{if $value eq 2} selected{/if}>{$lng.lbl_cod_keeping_and_confirmation}</option>
</select>
